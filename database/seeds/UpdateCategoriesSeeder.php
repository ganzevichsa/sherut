<?php

use Illuminate\Database\Seeder;

class UpdateCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            'חינוך' => '28_CATEGORY1.png',
            'חינוך מיוחד' => '28_CATEGORY2.png',
            'קהילה' => '28_CATEGORY3.png',
            'בריאות' => '28_CATEGORY4.png',
            'הדרכה' => '28_CATEGORY5.png',
            'ביטחון וממשלתי' => '28_CATEGORY6.png',
            'מדע וטכנולוגיה' => '28_CATEGORY7.png',
        ];
        foreach($data as $name => $icon) {
            $category = \App\Category::where('name',$name)->whereNull('job_type_id')->first();
            if($category) {
                $category->is_main = 1;
                $category->icon = $icon;
                $category->save();
            }
        }
    }
}
