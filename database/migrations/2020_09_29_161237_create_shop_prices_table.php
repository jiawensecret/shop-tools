<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShopPricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shop_prices', function (Blueprint $table) {
            $table->id();
            $table->integer('shop_id')->default(0)->index();
            $table->string('month',10)->default('')->comment('Y-m格式时间');
            $table->decimal('price',10,2)->default(0.00)->comment('广告成本');
            $table->unique(['shop_id','month']);
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
        Schema::dropIfExists('shop_prices');
    }
}
