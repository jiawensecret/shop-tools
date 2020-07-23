<?php

use Illuminate\Database\Seeder;

class AccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Model\Account::create([
            'account' => 'pay@utoooshop.com',
            'password' => '.Cm@bmVbcX68293',
            'account_type' => 'paypal',
            'charge_percent' => 0.01
        ]);
    }
}
