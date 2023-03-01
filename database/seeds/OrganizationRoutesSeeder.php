<?php

use Illuminate\Database\Seeder;

class OrganizationRoutesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $dataDefault = [
            'https://sherut-leumi.co.il' => 'חילוני',
            'https://aminadav.org.il' => 'דתי',
            'http://bat-ami.org.il/' => 'דתי',
            'http://hibur-hadash.org.il' => 'דתי',
            'https://ofekmashu.org.il' => 'חילוני',
            'http://www.shel.org.il/' => 'חילוני',
            'https://shlomit.org.il/' => 'חילוני',
        ];
        $data = [
            'שירות לאומי דתי',
            'שירות לאומי חילוני',
            'שירות אזרחי לחברה הערבית',
            'שירות לאומי לנוער בסיכון',
            'שירות לאומי לבעלי צרכים מיוחדים'
        ];
        foreach($dataDefault as $key => $value) {
            $organization = \App\Organization::where('website',$key)->first();
            $row = new \App\OrganizationRoute();
            $row->organization_id = $organization->id;
            $row->name = $value;
            $row->save();
        }
        foreach($data as $key => $value) {
            $row = new \App\OrganizationRoute();
            $row->name = $value;
            $row->save();
        }
    }
}
