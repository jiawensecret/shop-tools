<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReadExcelJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('read_excel_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('filename',50)->default('');
            $table->integer('user_id')->default(0);
            $table->integer('status')->default(0)->comment('1成功 2失败');
            $table->string('type',50)->default('');
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
        Schema::dropIfExists('read_excel_jobs');
    }
}
