<?php

use Illuminate\Database\Seeder;

class JobsTypeOfYearsSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $type_of_years = [
            'שנה נוכחית',
            'שנה הבאה',
            'ארכיון'
        ];
        foreach ($type_of_years as $type_of_year) {
            $year = new \App\TypeOfYear();
            $year->name = $type_of_year;
            $year->save();
        }
    }
}
