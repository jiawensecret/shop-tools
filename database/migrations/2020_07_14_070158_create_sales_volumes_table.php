<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesVolumesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales_volumes', function (Blueprint $table) {
            $table->id();
            $table->integer('person_id')->default(0)->index();
            $table->string('month',10)->default('')->comment('Y-m格式');
            $table->decimal('volume',10,2)->default(0.00)->comment('销售额');
            $table->decimal('total_cost',10,2)->default(0.00)->comment('总成本');
            $table->decimal('profit',10,2)->default(0.00)->comment('利润');
            $table->string('exchange',25)->default(0)->comment('汇率 美元对人民币');
            $table->unique(['person_id','month']);
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
        Schema::dropIfExists('sales_volumes');
    }
}
