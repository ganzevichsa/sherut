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
        $admin = new \App\Admin();
        $admin->email = 'admin@example.com';
        $admin->password = bcrypt('shirutbekalut123');
        $admin->name = 'Admin';
        $admin->save();
    }
}
