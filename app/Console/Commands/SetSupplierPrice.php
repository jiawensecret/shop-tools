<?php

namespace App\Console\Commands;

use App\Model\Support;
use App\Model\SupportPriceTender;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SetSupplierPrice extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'setSupplierPrice {--month=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '生成供应商采购价';

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
        $data = Support::select('sku',
            DB::raw("sum(total_price) total_price"),
            DB::raw("sum(total_cost) total_cost"),
            DB::raw("sum(count) count")
        )->groupBy('sku')
            ->get();

        foreach ($data as $v) {
            $item = [
                'sku' => $v['sku'],
            ];

            $price = [
                'sku' => $v['sku'],
                'price' => round(($v['total_price'] + $v['total_cost']) / $v['count'], 2)
            ];

            SupportPriceTender::updateOrCreate($item, $price);
        }

    }
}
