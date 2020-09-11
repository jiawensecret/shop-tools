<?php

namespace App\Console\Commands;

use App\Model\Order;
use App\Model\Shop;
use App\Services\Shopify;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ShopifyShipping extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shopify:shipping {--pid=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '获取订单的物流信息';

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

            $model = new Shopify($shop);
            Order::where('shop_id', $shop->id)
                ->where('shipping_status', 0)
                ->where('shopify_order_id','<>','')
                ->chunk(100, function ($orders) use ($model) {
                    foreach ($orders as $order) {
                        if (!is_null($this->option('pid')) && ($order->id % 10 != $this->option('pid'))) continue;
                        try{
                            $data = $model->getShippingByOrder($order->shopify_order_id);
                            $model->dealShipping($order,$data);
                        }catch (\Exception $exception) {
                            Log::error($exception->getMessage());
                        }
                    }
            });
        }
    }
}
