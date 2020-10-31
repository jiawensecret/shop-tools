<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaypalReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('paypal_reports', function (Blueprint $table) {
            $table->id();
            $table->integer('account_id')->default(0);
            $table->string('paypal_account_id',50)->default('');
            $table->string('transaction_id',50)->default('');
            $table->string('paypal_reference_id',50)->default('');
            $table->string('transaction_event_code',50)->default('');
            $table->decimal('transaction_amount',8,2)->default(0.00);
            $table->decimal('fee_amount',8,2)->default(0.00);
            $table->string('invoice_id',50)->default('');
            $table->timestamp('transaction_initiation_date');
            $table->timestamp('transaction_updated_date');
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
        Schema::dropIfExists('paypal_reports');
    }
}
