<?php

namespace App\Console\Commands;

use App\Model\Shop;
use App\Services\Shopify;
use App\SystemShopifyLog;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class GetOrdersTest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:orders {--shop=} {--start_time=} {--end_time=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '通过shopify api获取订单';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $shop_id = $this->option('shop');
        $start_time = $this->option('start_time');
        $end_time = $this->option('end_time');

        $shop = Shop::find($shop_id);

        try {
            $model = new Shopify($shop);

            [$data,$url] = $model->getOrders(Carbon::now()->subMonths(3)->toDateTimeString());

            $count = 0;
            foreach($data as $item){
                $time = Carbon::parse($item['created_at'])->toDateTimeString();
                if($time >= $start_time && $time <= $end_time) {
                    ++$count;
                    dump($item['order_number']);
                }

            }
            while($url) {
                $shopifyLog = SystemShopifyLog::create([
                    'url' => $url,
                    'command' => $this->signature,
                    'shop_id' => $shop->id,
                    'pid' => intval($this->option('pid')) ?: 0
                ]);
                [$data,$url] = $model->getOrdersByUrl($url,$shopifyLog);

                foreach($data as $item){
                    $time = Carbon::parse($item['created_at'])->toDateTimeString();
                    if($time >= $start_time && $time <= $end_time) {
                        ++$count;
                        dump($item['order_number']);
                    }
                }
            }
            dump($count);
        } catch (\Exception $exception) {
            dump('error');
            Log::error($exception->getMessage());
        }

    }
}
