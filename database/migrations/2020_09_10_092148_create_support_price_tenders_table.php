<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSupportPriceTendersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('support_price_tenders', function (Blueprint $table) {
            $table->id();
            $table->string('sku',50)->default('')->index()->comment('sku');
            $table->string('month',50)->default('')->comment('月份');
            $table->decimal('price',10,2)->default(0);
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
        Schema::dropIfExists('support_price_tenders');
    }
}
