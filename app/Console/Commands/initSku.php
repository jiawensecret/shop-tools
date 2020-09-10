<?php

namespace App\Console\Commands;

use App\Model\OrderGoods;
use Illuminate\Console\Command;

class initSku extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'init:sku';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'sku预处理';

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
        OrderGoods::chunk(100, function ($goods) {
            foreach ($goods as $v) {
                $sku = preg_replace("[`•~!@#$%^&*()+=|{}\':;\',\\[\\]\.<>/?~！@#￥%……&*（）——+|{}【】‘；：”“’。 ，、？]",'',$v->sku);
                $sku = str_replace('(','',$sku);
                $sku = str_replace(')','',$sku);
                $sku = str_replace('/','',$sku);
                $sku = str_replace('.','',$sku);
                $sku = str_replace('•','',$sku);
                $sku = str_replace(' ','',$sku);
                $v->sku_deal = $sku;
                $v->save();
            }
        });
    }
}
