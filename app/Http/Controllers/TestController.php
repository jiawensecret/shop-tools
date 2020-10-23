<?php

namespace App\Http\Controllers;

use App\Imports\OrdersImport;
use App\Imports\TransportPriceImport;
use App\Imports\TransportsImport;
use App\Model\Order;
use App\Model\SaleVolumeOrderLog;
use App\Model\Shop;
use App\Services\SaleVolume;
use App\Services\Shopify;
use App\SystemShopifyLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;


class TestController extends Controller
{
    public function index(){
        $shop_id = 4;
        $start_time = '2020-08-01';
        $end_time = '2020-09-01';

        $shop = Shop::find($shop_id);

        try {
            $model = new Shopify($shop);

            [$data,$url] = $model->getOrders(Carbon::now()->subMonths(3)->toDateTimeString());

            $count = 0;
            foreach($data as $item){
                $time = Carbon::parse($item['created_at'])->toDateTimeString();
                if($time >= $start_time && $time <= $end_time) {
                    ++$count;
                    echo $item['order_number'];
                }

            }
            while($url) {
                $shopifyLog = SystemShopifyLog::create([
                    'url' => $url,
                    'command' => '',
                    'shop_id' => $shop->id,
                    'pid' => 0
                ]);
                [$data,$url] = $model->getOrdersByUrl($url,$shopifyLog);

                foreach($data as $item){
                    $time = Carbon::parse($item['created_at'])->toDateTimeString();
                    if($time >= $start_time && $time <= $end_time) {
                        ++$count;
                        echo $item['order_number'];
                    }
                }
            }
            dump('count:'.$count);
        } catch (\Exception $exception) {
            echo 'error';
            Log::error($exception->getMessage());
        }
    }

    public function transport()
    {


        $exchange = 10;

        $value = SaleVolumeOrderLog::where('month','2020-06')
            ->select('sales_volume_id',
                DB::raw("SUM(order_price -refund -pay_charge) * {$exchange} as volume"),
                DB::raw('SUM(cost_price+transport_price+ad_price+shop_charge) as total_cost'),
                DB::raw('SUM(profit) as total_profit'))
            ->groupBy('sales_volume_id')
            ->get();
        dd($value);



        $raw = '(order_price - pay_charge) *' . $exchange .'-cost_price-transport_price-ad_price-shop_charge';
        SaleVolumeOrderLog::where('order_price','>',0)->update(['profit' => DB::raw($raw)]);
        exit;
        ini_set('memory_limit', '512M');
        set_time_limit(0);
        Excel::import(new TransportsImport, storage_path('app/test3.xlsx'));
        echo 222;exit;
    }

    public function price()
    {
        ini_set('memory_limit', '512M');
        set_time_limit(0);
        Excel::import(new TransportPriceImport, storage_path('app/test2.xlsx'));
        echo 333;exit;
    }

    public function testNew()
    {
        ini_set('memory_limit', '512M');
        set_time_limit(0);
        Excel::import(new OrdersImport, storage_path('app/订单列表1-3.xlsx'));
        echo 111;exit;
    }
}
