<?php

use Illuminate\Database\Seeder;

class AuthProvidersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            'facebook', 'google'
        ];
        foreach ($data as $item) {
            $provider = new \App\AuthProvider();
            $provider->name = $item;
            $provider->save();
        }
    }
}
