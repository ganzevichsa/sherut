<?php

use Illuminate\Database\Seeder;

class YearsSeeder extends Seeder
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
                'key' => 'this_year',
                'carbon' => \Carbon\Carbon::now(),
                'name' => 'תשפ״א'
            ],
            [
                'key' => 'next_year',
                'carbon' => (new \Carbon\Carbon())->addYear(),
                'name' => 'תשפ״ב'
            ]
        ];
        foreach($data as $key => $value) {
            $year = new \App\Year();
            $year->key = $value['key'];
            $year->carbon = $value['carbon'];
            $year->name = $value['name'];
            $year->save();
        }
    }
}
