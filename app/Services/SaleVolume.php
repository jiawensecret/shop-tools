<?php


namespace App\Services;


use App\Model\AdPrice;
use App\Model\Order;
use App\Model\Person;
use App\Model\RefundOrder;
use App\Model\SalesVolume;
use App\Model\SaleVolumeOrderLog;
use App\Model\Shop;
use App\Model\ShopPrice;
use App\Model\SupportPriceTender;
use App\Model\Transport;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SaleVolume
{
    protected $month = '';

    protected $startTime = '';
    protected $endTime = '';

    protected $shopToPerson = [];

    protected $shopFee = [];

    protected $accountFee = [];


    public function __construct($month)
    {
        $this->month = $month;
        $this->startTime = Carbon::parse($month)->subMonths(3)->toDateTimeString();
        $this->endTime = Carbon::parse($month)->addMonth()->toDateTimeString();
    }

    protected function setAttr($order)
    {
        $shop = $order->shop;
        if (!$shop) {
            return false;
        }
        $person = $shop->person;
        if (!$person) {
            return false;
        }

        $account = $shop->account;
        if (!$person) {
            return false;
        }

        $this->shopToPerson[$shop->id] = $person->id;

        return true;
    }

    public function builtOrderLog()
    {
        $orders = Order::where('pay_time', '>', $this->startTime)
            ->where('pay_time', '<', $this->endTime)
            ->where('is_volume', Order::NOT_VOLUME)
            ->get();

        foreach ($orders as $order) {

            $transport = $order->transport;
            if (!count($transport)) continue;

//            $i = 0;
//            foreach ($transport as $value) {
//                if ($value->status != Transport::GOT) {
//                    $i = 1;
//                    break;
//                }
//            }
//            if ($i == 1) continue;

            if (!isset($this->shopToPerson[$order->shop_id])) {
                if (!$this->setAttr($order)) continue;
            }

            $data = [
                'person_id' => $this->shopToPerson[$order->shop_id],
                'month' => $this->month
            ];

            $volume = SalesVolume::firstOrCreate($data);

            $orderGoods = $order->goods;
            $cost = 0;
            foreach ($orderGoods as $item) {
                $supportTender = SupportPriceTender::where('sku', $item['sku'])->first();
//                if (!$supportTender) {
//                    $supportTender = SupportPriceTender::where('sku', $item['sku_deal'])->first();
//                }
//                $aPrice = $supportTender['price'] ?? 0;
//
//                $supplierPrice = $item->supplier_price ?: $aPrice;

                $supplierPrice = $supportTender['price'] ?? $item->supplier_price;
                $cost += $item->count * $supplierPrice;
                unset($supportTender);
            }

            $data = [
                'order_price' => $order->order_price,
                'month' => $this->month,
                'cost_price' => $cost,
                'transport_price' => $transport->sum('transport_price'),
                'pay_charge' => $order->fee_amount,
                'refund' => $order->refund_price,
                'order_create_time' => $order->order_time
            ];

            $volume->log()->updateOrCreate(['order_id' => $order->id, 'shop_id' => $order->shop_id], $data);

            $order->is_volume = Order::VOLUMED;
            $order->save();
        }
    }

    public function updateLogs()
    {
        SaleVolumeOrderLog::where('month',$this->month)->chunk(100, function ($logs) {
            foreach ($logs as $log) {
                $order = Order::find($log->order_id);

                if ($order) {

                    $transport = $order->transport;
                    if (!count($transport)) continue;

                    $orderGoods = $order->goods;
                    $cost = 0;
                    foreach ($orderGoods as $item) {
                        $supportTender = SupportPriceTender::where('sku', $item['sku'])->first();
//                        if (!$supportTender) {
//                            $supportTender = SupportPriceTender::where('sku', $item['sku_deal'])->first();
//                        }

//                        $aPrice = $supportTender['price'] ?? 0;
//
//                        $supplierPrice = $item->supplier_price ?: $aPrice;

                        $supplierPrice = $supportTender['price'] ?? $item->supplier_price;

                        $cost = $cost + $item['count'] * $supplierPrice;
                        unset($supportTender);

                        Log::info('tender:'.$item->supplier_price.'|||'.$supplierPrice.'|||'.$item['count']);
                    }

                    $data = [
                        'order_price' => $order->order_price,
                        'month' => $this->month,
                        'cost_price' => $cost,
                        'transport_price' => $transport->sum('transport_price'),
                        'pay_charge' => $order->fee_amount,
                        'refund' => $order->refund_price,
                        'shop_charge' => 0
                    ];

                    unset($cost);

                    $log->update($data);
                }
            }
        });
    }

    public function calculate($exchange)
    {
        //广告费用
        $adPrice = AdPrice::where('month', $this->month)->get();
        foreach ($adPrice as $price) {
            $count = SaleVolumeOrderLog::where('month', $this->month)
                ->where('shop_id', $price->shop_id)
                ->count();
            if (!$count) continue;

            $perPrice = round($price->price / $count, 2);
            if ($price->type == 1) $perPrice = round($perPrice * $exchange, 2);

            SaleVolumeOrderLog::where('month', $this->month)
                ->where('shop_id', $price->shop_id)
                ->update(['ad_price' => $perPrice]);
        }

        //店铺手续费
        $shopPrice = ShopPrice::where('month', $this->month)->get();
        foreach ($shopPrice as $price) {
            $count = SaleVolumeOrderLog::where('month', $this->month)
                ->where('shop_id', $price->shop_id)
                ->count();
            if (!$count) continue;

            $perPrice = round($price->price / $count, 2);

            SaleVolumeOrderLog::where('month', $this->month)
                ->where('shop_id', $price->shop_id)
                ->update(['shop_charge' => $perPrice]);
        }

        //退款信息
        $refundOrder = RefundOrder::where('month',$this->month)->get();
        foreach ($refundOrder as $item) {
            $data = [
                'person_id' => Shop::find($item->shop_id)->person_id ?? 0,
                'month' => $this->month,
            ];

            $volume = SalesVolume::firstOrCreate($data);

            $data = [
                'order_price' => 0,
                'month' => $this->month,
                'cost_price' => 0,
                'transport_price' => 0,
                'pay_charge' => 0,
                'refund' => $item->refund,
                'order_create_time' => Carbon::now()
            ];

            $volume->log()->updateOrCreate(['refund_order_id' => '虚拟:'.$item->order_id, 'shop_id' => $item->shop_id],$data);
        }


        //计算利润
        $raw = '(order_price - pay_charge - refund - shop_charge) *' . $exchange . '-cost_price-transport_price-ad_price';
        SaleVolumeOrderLog::where('month', $this->month)
            ->update(['profit' => DB::raw($raw)]);
    }

    public function totalReport($exchange)
    {
        $volumes = SaleVolumeOrderLog::where('month', $this->month)
            ->select('sales_volume_id',
                DB::raw("SUM(order_price-pay_charge) as volume"),
                DB::raw("SUM(cost_price+transport_price+ad_price+shop_charge) / {$exchange} as total_cost"),
                DB::raw("SUM(profit) / {$exchange} as total_profit"),
                DB::raw("SUM(order_price) as order_price"),
                DB::raw("SUM(refund) as refund"),
                DB::raw("SUM(pay_charge) as pay_charge"),
                DB::raw("SUM(cost_price) / {$exchange} as cost_price"),
                DB::raw("SUM(transport_price) / {$exchange} as transport_price"),
                DB::raw("SUM(ad_price) / {$exchange} as ad_price"),
                DB::raw("SUM(shop_charge) as shop_charge")
            )
            ->groupBy('sales_volume_id')
            ->get();

        foreach ($volumes as $volume) {
            $report = SalesVolume::find($volume->sales_volume_id);
            $report->volume = $volume->volume;
            $report->total_cost = $volume->total_cost;
            $report->profit = $volume->total_profit;
            $report->exchange = $exchange;
            $report->order_price = $volume->order_price;
            $report->refund = $volume->refund;
            $report->pay_charge = $volume->pay_charge;
            $report->cost_price = $volume->cost_price;
            $report->transport_price = $volume->transport_price;
            $report->ad_price = $volume->ad_price;
            $report->shop_charge = $volume->shop_charge;

            $report->save();
        }
    }
}
