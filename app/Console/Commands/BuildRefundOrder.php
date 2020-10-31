<?php

namespace App\Console\Commands;

use App\Model\Order;
use App\Model\PaypalReport;
use App\Model\RefundOrder;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Shopify\Object\Refund;

class BuildRefundOrder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'refund:order {--date=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '生成退货订单';

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
        if (!is_null($date = $this->option('date'))) {
            $month = Carbon::parse($date)->firstOfMonth()->submonth()->format('Y-m');

            $start = Carbon::parse($date)->firstOfMonth()->submonth()->toDateTimeString();

            $end = Carbon::parse($date)->firstOfMonth()->toDateTimeString();
        } else {
            $month = Carbon::now()->firstOfMonth()->submonth()->format('Y-m');

            $start = Carbon::now()->firstOfMonth()->submonth()->toDateTimeString();

            $end = Carbon::now()->firstOfMonth()->toDateTimeString();
        }


        PaypalReport::where('transaction_initiation_date','>',$start)
            ->where('transaction_initiation_date','<=',$end)
            ->whereIn('transaction_event_code',['T1107','T1106'])
            ->chunk(100,function ($reports) use ($month){
                foreach ($reports as $report) {
                    $order = Order::where('sale_no',$report['paypal_reference_id'])->first();
                    if ($order) {
                        $value = [
                            'order_id' => $order->id,
                            'shop_id' => $order->shop_id,
                            'month' => $month,
                            'refund' => abs($report['transaction_amount'] + $report['fee_amount'])
                        ];
                        RefundOrder::updateOrCreate(['order_id' => $order->id,'month' => $month ],$value);
                    }
                }
            });

        dump($month);
        dump($start);
        dump($end);

    }
}
