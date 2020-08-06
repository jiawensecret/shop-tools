<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShopsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shops', function (Blueprint $table) {
            $table->id();
            $table->integer('person_id')->default(0)->index();
            $table->integer('account_id')->default(0)->comment('收款账号');
            $table->string('name',25)->default('')->comment('');
            $table->string('code',25)->default('')->comment('商铺编码');
            $table->string('uri',100)->default('')->comment('');
            $table->string('desc',255)->default('')->comment('');
            $table->decimal('charge_percent',10,4)->default(0.0000)->comment('店铺手续费百分比');
            $table->unique(['code']);
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
        Schema::dropIfExists('shops');
    }
}
