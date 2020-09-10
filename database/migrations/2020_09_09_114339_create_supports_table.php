<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSupportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('supports', function (Blueprint $table) {
            $table->id();
            $table->string('support_code',25)->default('')->comment('采购单号');
            $table->string('goods_code',25)->default('')->comment('商品编码');
            $table->string('sku',100)->default('')->comment('sku');
            $table->decimal('price',10,2)->default(0.00)->comment('单价');
            $table->integer('count')->default(0)->comment('数量');
            $table->decimal('total_price',10,2)->default(0.00)->comment('总价');
            $table->decimal('transport_price',10,2)->default(0.00)->comment('运费');
            $table->decimal('other_price',10,2)->default(0.00)->comment('额外费用');
            $table->decimal('discount',10,2)->default(0.00)->comment('折扣金额');
            $table->decimal('total_cost',10,2)->default(0.00)->comment('运费-折扣+额外费用取平均');
            $table->string('order_code',50)->default('')->comment('1688订单号');
            $table->timestamp('order_time')->nullable()->comment('采购时间');
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
        Schema::dropIfExists('supports');
    }
}
