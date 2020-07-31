<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdminsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admins', function (Blueprint $table) {
            $table->id();
            $table->string('username',25);
            $table->string('password',255);
            $table->string('name',25)->default('');
            $table->string('avatar',255)->default('');
            $table->string('email',255)->default('');
            $table->string('phone',20)->default('');
            $table->tinyInteger('is_admin')->default(0);
            $table->tinyInteger('role_id')->default(0);
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
        Schema::dropIfExists('admins');
    }
}
