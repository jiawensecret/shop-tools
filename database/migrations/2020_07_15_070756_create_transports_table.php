<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transports', function (Blueprint $table) {
            $table->id();
            $table->integer('order_id')->default(0)->index();
            $table->integer('order_goods_id')->default(0)->index();
            $table->string('package_code',80)->default('')->index()->comment('包裹号');
            $table->string('transport_no',80)->default('')->index()->comment('运单号');
            $table->string('order_no',80)->default('')->index()->comment('订单号');
            $table->string('transport_name',255)->default('')->comment('物流方式');
            $table->string('new_info',500)->default('')->comment('最新消息');
            $table->string('country',50)->default('')->comment('国家');
            $table->string('consignee',255)->default('')->comment('收货人');

            $table->string('status_text',25)->default('')->comment('状态消息');
            $table->tinyInteger('status')->default(0)->index()->comment('状态');

            $table->decimal('transport_price',10,2)->default(0.00)->comment('运费');

            $table->unique(['package_code','order_no']);
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
        Schema::dropIfExists('transports');
    }
}
