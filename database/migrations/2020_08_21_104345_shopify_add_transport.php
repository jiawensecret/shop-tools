<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ShopifyAddTransport extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transports', function (Blueprint $table) {
            $table->string('shopify_fulfillment_id',25)->default('')->comment('');
            $table->string('shopify_fulfillment_status',25)->default('')->comment('');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transports', function (Blueprint $table) {
            $table->dropColumn('shopify_fulfillment_id');
            $table->dropColumn('shopify_fulfillment_status');
        });
    }
}
