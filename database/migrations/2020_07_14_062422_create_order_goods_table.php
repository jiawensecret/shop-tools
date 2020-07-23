<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderGoodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_goods', function (Blueprint $table) {
            $table->id();
            $table->integer('order_id')->default(0)->index();
            $table->string('order_no',80)->default('')->comment('订单号');
            $table->string('package_code',80)->default('')->index()->comment('包裹号');
            $table->string('transport_no',80)->default('')->index()->comment('运单号');
            $table->string('sku',255)->default('')->comment('商品sku');
            $table->string('product_no',255)->default('')->comment('商品号');
            $table->string('product_code',50)->default('')->comment('商品编码');
            $table->string('product_name',255)->default('')->comment('产品名称');
            $table->decimal('price',10,2)->default(0.00)->comment('产品售价');
            $table->integer('count')->default(0)->comment('产品数量');
            $table->string('size',255)->default('')->comment('商品规格');
            $table->string('pic',500)->default('')->comment('产品图片地址');
            $table->string('sale_name',255)->default('')->comment('商品名称');
            $table->decimal('supplier_price',10,2)->default(0.00)->comment('供应价格 采购价');

            $table->unique(['order_no','sku','pic']);
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
        Schema::dropIfExists('order_goods');
    }
}
