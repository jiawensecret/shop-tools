<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ShopifyAddFeeAmount extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('fee_amount',10,4)->default(0.0000)->comment('支付费率');
            $table->tinyInteger('is_transactions')->default(0)->comment('是否成功获取交易接口 交易接口只跑一次');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('fee_amount');
            $table->dropColumn('is_transactions');
        });
    }
}
