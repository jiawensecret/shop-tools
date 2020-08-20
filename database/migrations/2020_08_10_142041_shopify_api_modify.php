<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ShopifyApiModify extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shops', function (Blueprint $table) {
            $table->string('dxm_id',25)->default('')->comment('店小秘id');
            $table->string('client_id',255)->default('')->comment('client_id');
            $table->string('client_password',255)->default('')->comment('access token');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->string('shopify_order_id',25)->default('')->comment('');
            $table->string('checkout_id',25)->default('')->comment('');
        });

        Schema::table('order_goods', function (Blueprint $table) {
            $table->string('shopify_order_goods_id',25)->default('')->comment('');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shops', function (Blueprint $table) {
            $table->dropColumn('dxm_id');
            $table->dropColumn('client_id');
            $table->dropColumn('client_password');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('shopify_order_id');
        });

        Schema::table('order_goods', function (Blueprint $table) {
            $table->dropColumn('shopify_order_goods_id');
        });
    }
}
