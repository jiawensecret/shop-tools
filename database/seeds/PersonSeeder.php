<?php

use Illuminate\Database\Seeder;

class PersonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Model\Person::create([
            'name' => '测试',
            'is_active' => 1,
        ]);
    }
}
