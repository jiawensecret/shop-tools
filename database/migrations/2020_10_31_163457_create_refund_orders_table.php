<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRefundOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('refund_orders', function (Blueprint $table) {
            $table->id();
            $table->integer('order_id')->default(0)->index();
            $table->integer('shop_id')->default(0)->index();
            $table->string('month',10)->default('')->index()->comment('Y-m格式');
            $table->decimal('refund',10,2)->default(0.00)->comment('退款');
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
        Schema::dropIfExists('refund_orders');
    }
}
