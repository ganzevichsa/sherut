<?php

use Illuminate\Database\Seeder;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = [
            'Admin',
            'HR',
            'רוצה למצוא שירות לאומי',
            'רוצה מדרשה',
            'סיימתי שירות',
            'באמצע שירות',
        ];
        foreach($roles as $role) {
            $roleObj = new \App\Role();
            $roleObj->name = $role;
            $roleObj->save();
        }
    }
}
