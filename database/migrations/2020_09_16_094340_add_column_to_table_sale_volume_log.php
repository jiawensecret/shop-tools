<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnToTableSaleVolumeLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sales_volumes', function (Blueprint $table) {
            $table->decimal('order_price',10,2)->default(0.00)->comment('订单售价');
            $table->decimal('cost_price',10,2)->default(0.00)->comment('订单成本价');
            $table->decimal('transport_price',10,2)->default(0.00)->comment('运输成本');
            $table->decimal('ad_price',10,2)->default(0.00)->comment('广告成本');
            $table->decimal('shop_charge',10,2)->default(0.00)->comment('店铺手续费成本');
            $table->decimal('pay_charge',10,2)->default(0.00)->comment('收款手续费');
            $table->decimal('refund',10,2)->default(0.00)->comment('退款');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sales_volumes', function (Blueprint $table) {
            $table->dropColumn('order_price');
            $table->dropColumn('cost_price');
            $table->dropColumn('ad_price');
            $table->dropColumn('transport_price');
            $table->dropColumn('shop_charge');
            $table->dropColumn('pay_charge');
            $table->dropColumn('refund');
        });
    }
}
