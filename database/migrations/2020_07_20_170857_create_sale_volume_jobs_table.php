<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSaleVolumeJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sale_volume_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('month',10)->default('')->comment('Y-m格式');
            $table->string('exchange',25)->default(0)->comment('汇率 美元对人民币');
            $table->integer('status')->default(0)->comment('1成功 2失败');
            $table->text('error_msg')->nullable();
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
        Schema::dropIfExists('sale_volume_jobs');
    }
}
