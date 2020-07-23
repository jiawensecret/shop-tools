<?php

use Illuminate\Database\Seeder;

class ShopSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Model\Shop::create([
            'person_id' => 1,
            'account_id' => 1,
            'name' => '测试用店铺',
            'code' => 'Cuckoosports',
        ]);
    }
}
