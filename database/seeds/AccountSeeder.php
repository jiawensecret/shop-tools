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
            'charge_percent' => 0.01,
            'client_id' => 'AW8t93BVUNSE2RHxopAcaC2Ot6B38uKu8cf8LbnLTGbKG2PnhkFDRJfxAnvFUEjHt2NfeJjNHWAJpbyx',
            'client_password' => 'EMm7dohzuCmCJk_7N1nmbu-y4XsHNyJqU4QPQhIlWKtqvhFDDwFg78QiaANTEPj9WlvnPhF5ce89Cuv3'
        ]);
    }
}
