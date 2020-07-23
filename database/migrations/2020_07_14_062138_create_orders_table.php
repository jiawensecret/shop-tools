<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_no',80)->default('')->comment('订单号');
            $table->integer('shop_id')->default(0)->index()->comment('店铺id');
            $table->string('sale_no',80)->default('')->comment('交易号');
            $table->string('status_text',25)->default('')->comment('状态消息');
            $table->tinyInteger('status')->default(0)->index()->comment('状态');
            $table->string('channel',25)->default('')->comment('平台渠道');
            $table->string('desc',255)->default('')->comment('备注');
            $table->timestamp('order_time')->nullable()->comment('下单时间');
            $table->timestamp('pay_time')->nullable()->comment('支付时间');
            $table->timestamp('post_time')->nullable()->comment('提交时间');
            $table->timestamp('refund_time')->nullable()->comment('退款时间');
            $table->string('pay_type',150)->default('')->comment('付款方式');
            $table->decimal('order_price',10,2)->default(0.00)->comment('订单售价');
            $table->decimal('sale_transport_price',10,2)->default(0.00)->comment('顾客支付运费');
            $table->decimal('refund_price',10,2)->default(0.00)->comment('退款金额');
            //地址相关

            $table->string('custom_account',50)->default('')->comment('买家账号');
            $table->string('custom_name',255)->default('')->comment('买家姓名');
            $table->string('custom_email',50)->default('')->comment('买家email');
            $table->string('custom_transport_name',255)->default('')->comment('买家指定物流');
            $table->string('transport_name',255)->default('')->comment('物流方式');

            $table->string('consignee',255)->default('')->comment('收货人');
            $table->string('consignee_address',255)->default('')->comment('收货人详细地址');
            $table->string('consignee_city',50)->default('')->comment('收货人城市');
            $table->string('consignee_province',50)->default('')->comment('收货人详细省份');
            $table->string('consignee_code',50)->default('')->comment('收货人邮编');
            $table->string('consignee_country',50)->default('')->comment('收货人国家');
            $table->string('consignee_country_code',50)->default('')->comment('收货人国家code');
            $table->string('consignee_phone',50)->default('')->comment('收货人电话');
            $table->string('consignee_tel',50)->default('')->comment('收货人手机');

            $table->tinyInteger('is_volume')->default(0)->index()->comment('是否计算过绩效 0未计算 1已计算');
            $table->unique(['order_no']);
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
        Schema::dropIfExists('orders');
    }
}
