<?php

namespace App\Console\Commands;

use App\Model\Order;
use App\Model\Shop;
use App\Services\Shopify;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ShopifyPayment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shopify:payment {--pid=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '获取订单支付数据';

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
            $query = Order::where('shop_id', $shop->id)
                ->where('is_transactions', 0)
                ->where('shopify_order_id', '<>', '');
            if (!is_null($this->option('pid'))) {
                $query->where('pid',$this->option('pid'));
            }

            $query->chunk(100, function ($orders) use ($model) {
                foreach ($orders as $order) {
                    try {
                        $data = $model->getPaymentByOrder($order->shopify_order_id);
                        $model->dealPayment($order, $data);
                    } catch (\Exception $exception) {
                        Log::error($exception->getMessage());
                    }
                }
            });
        }
    }
}
