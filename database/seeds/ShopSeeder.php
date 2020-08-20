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

        \App\Model\Shop::create([
            'person_id' => 1,
            'account_id' => 1,
            'name' => 'test',
            'code' => 'woiglasses',
            'dxm_id' => 'test',
            'client_id' => '12',
            'client_password' => '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',
        ]);
    }
}
