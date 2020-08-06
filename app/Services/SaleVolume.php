<?php


namespace App\Services;


use App\Model\AdPrice;
use App\Model\Order;
use App\Model\SalesVolume;
use App\Model\SaleVolumeOrderLog;
use App\Model\Transport;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

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
        $this->shopFee[$shop->id] = $shop->charge_percent;
        $this->accountFee[$shop->id] = $account->charge_percent;

        return true;
    }

    public function builtOrderLog()
    {
        $orders = Order::where('pay_time','>',$this->startTime)
            ->where('pay_time','<', $this->endTime)
            ->where('is_volume',Order::NOT_VOLUME)
            ->get();

        foreach ($orders as $order) {

            $transport = $order->transport;
            if (!count($transport)) continue;

            $i = 0;
            foreach ($transport as $value){
                if ($value->status != Transport::GOT) {
                    $i = 1;
                    break;
                }
            }
            if ($i == 1) continue;

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
                $cost += $item->count * $item->supplier_price;
            }

            $pay_percent = $this->accountFee[$order->shop_id] ?? 0;
            $data = [
                'order_price' => $order->order_price,
                'month' => $this->month,
                'cost_price' => $cost,
                'transport_price' => $transport->sum('transport_price'),
                'pay_charge' => round($pay_percent * $order->order_price,2),
                'refund' => $order->refund_price,
            ];

            $volume->log()->updateOrCreate(['order_id' => $order->id,'shop_id' => $order->shop_id],$data);

            $order->is_volume = Order::VOLUMED;
            $order->save();
        }
    }

    public function calculate($exchange)
    {
        //广告费用
        $adPrice = AdPrice::where('month',$this->month)->get();
        foreach ($adPrice as $price){
            $count = SaleVolumeOrderLog::where('month',$this->month)
                ->where('shop_id',$price->shop_id)
                ->count();
            if (!$count) continue;

            $perPrice = round($price->price / $count,2);
            if ($price->type == 1) $perPrice = round($perPrice * $exchange);

            SaleVolumeOrderLog::where('month',$this->month)
                ->where('shop_id',$price->shop_id)
                ->update(['ad_price' =>$perPrice]);
        }

        //计算利润
        $raw = '(order_price - pay_charge - refund) *' . $exchange .'-cost_price-transport_price-ad_price-shop_charge';
        SaleVolumeOrderLog::where('month',$this->month)
            ->update(['profit' => DB::raw($raw)]);
    }

    public function totalReport($exchange)
    {
        $volumes = SaleVolumeOrderLog::where('month',$this->month)
            ->select('sales_volume_id',
                DB::raw("SUM(order_price-refund-pay_charge) * {$exchange} as volume"),
                DB::raw('SUM(cost_price+transport_price+ad_price+shop_charge) as total_cost'),
                DB::raw('SUM(profit) as total_profit'))
            ->groupBy('sales_volume_id')
            ->get();

        foreach ($volumes as $volume) {
            $report = SalesVolume::find($volume->sales_volume_id);
            $report->volume = $volume->volume;
            $report->total_cost = $volume->total_cost;
            $report->profit = $volume->total_profit;
            $report->exchange = $exchange;

            $report->save();
        }
    }
}
