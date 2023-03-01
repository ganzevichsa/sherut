<?php

use Illuminate\Database\Seeder;

class JobTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [
                'name' => 'Before School',
                'role_id' => \App\Role::USER_BEFORE_SCHOOL,
            ],
            [
                'name' => 'After School',
                'role_id' => \App\Role::USER_AFTER_SCHOOL,
            ]
        ];
        foreach($data as $key => $data) {
            $jobType = new \App\JobType();
            $jobType->name = $data['name'];
            $jobType->role_id = $data['role_id'];
            $jobType->save();
        }
    }
}
