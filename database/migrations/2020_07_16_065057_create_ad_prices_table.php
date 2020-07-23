<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdPricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ad_prices', function (Blueprint $table) {
            $table->id();
            $table->integer('shop_id')->default(0)->index();
            $table->string('month',10)->default('')->comment('Y-m格式时间');
            $table->decimal('price',10,2)->default(0.00)->comment('广告成本');
            $table->tinyInteger('type')->default(0)->comment('0人民币 1美元');
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
        Schema::dropIfExists('ad_prices');
    }
}
