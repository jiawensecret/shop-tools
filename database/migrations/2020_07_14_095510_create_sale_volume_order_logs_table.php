<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSaleVolumeOrderLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sale_volume_order_logs', function (Blueprint $table) {
            $table->id();
            $table->integer('sales_volume_id')->default(0)->index();
            $table->integer('order_id')->default(0)->index();
            $table->integer('shop_id')->default(0)->index();
            $table->string('month',10)->default('')->index()->comment('Y-m格式');
            $table->decimal('order_price',10,2)->default(0.00)->comment('订单售价');
            $table->decimal('cost_price',10,2)->default(0.00)->comment('订单成本价');
            $table->decimal('transport_price',10,2)->default(0.00)->comment('运输成本');
            $table->decimal('ad_price',10,2)->default(0.00)->comment('广告成本');
            $table->decimal('shop_charge',10,2)->default(0.00)->comment('店铺手续费成本');
            $table->decimal('pay_charge',10,2)->default(0.00)->comment('收款手续费');
            $table->decimal('refund',10,2)->default(0.00)->comment('退款');
            $table->decimal('profit',10,2)->default(0.00)->comment('利润');
            $table->string('desc',255)->default('')->comment('详情');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sale_volume_order_logs');
    }
}
