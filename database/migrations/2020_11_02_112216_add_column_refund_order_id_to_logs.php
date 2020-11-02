<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnRefundOrderIdToLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sale_volume_order_logs', function (Blueprint $table) {
            $table->string('refund_order_id',25)->default('');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sale_volume_order_logs', function (Blueprint $table) {
            $table->dropColumn('refund_order_id');
        });
    }
}
