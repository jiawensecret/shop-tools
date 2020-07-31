<?php

use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Model\Admin::Create([
            'username' => 'admin',
            'password' => bcrypt('123456'),
            'name' => '管理员',
            'is_admin' => 1
        ]);
    }
}
