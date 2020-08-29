<?php

namespace App\Jobs;

use App\Model\Shop;
use App\Services\Shopify;
use App\SystemShopifyLog;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DealErrorCommand implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $log;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($log)
    {
        $this->log = $log;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        ini_set('memory_limit', '512M');
        set_time_limit(0);

        try {
            $shop = Shop::find($this->log->shop_id);
            $model = new Shopify($shop);

            [$data,$url] = $model->getOrders(Carbon::now()->subMonths(3)->toDateTimeString());

            foreach($data as $item){
                $model->dealOrder($item);
            }
            $this->log->is_success = 1;
            $this->log->save();

            while($url) {
                $shopifyLog = SystemShopifyLog::create([
                    'url' => $url,
                    'command' => $this->log->command,
                    'shop_id' => $shop->id,
                    'type' => 1
                ]);
                [$data,$url] = $model->getOrdersByUrl($url,$shopifyLog);

                foreach($data as $item){
                    $model->dealOrder($item);
                }
            }
        } catch (\Exception $exception) {

        }
    }
}
