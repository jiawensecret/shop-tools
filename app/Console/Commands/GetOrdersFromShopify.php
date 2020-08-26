<?php

namespace App\Console\Commands;

use App\Model\Shop;
use App\Services\Shopify;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class GetOrdersFromShopify extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shopify:orders';

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
        $shops = Shop::all();
        foreach ($shops as $shop) {
            if (empty($shop->client_password) || empty($shop->dxm_id)) continue;
            try {
                $model = new Shopify($shop);

                [$data,$url] = $model->getOrders(Carbon::now()->subMonths(3)->toDateTimeString());

                foreach($data as $item){
                    $model->dealOrder($item);
                }
                while($url) {
                    [$data,$url] = $model->getOrdersByUrl($url);

                    foreach($data as $item){
                        $model->dealOrder($item);
                    }
                }
            } catch (\Exception $exception) {
                Log::error($exception->getMessage());
            }
        }
    }
}
