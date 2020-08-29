<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSystemShopifyLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('system_shopify_logs', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('pid')->default(0)->comment('进程序号');
            $table->integer('shop_id')->default(0)->comment('shop id');
            $table->string('url',500);
            $table->tinyInteger('is_success')->default(0)->comment('1成功 2失败');
            $table->string('command',50)->default('');
            $table->tinyInteger('type')->default(0)->comment('0为计划任务 1位job');

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
        Schema::dropIfExists('system_shopify_logs');
    }
}
