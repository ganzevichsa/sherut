<?php

namespace App\Exports;

use App\Job;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Support\Collection;
use App\Category;
use App\City;
use App\Subcategory;
use App\Organization;

class JobsExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $items = Job::whereNull('title_old')->where('nucleus', 'כן')->get();

        foreach ($items as $item)
        {
            $order[] = [
                $item->id,
                $item->site,
                $item->title,
                $item->about,
                Category::find($item->category_id)->name??'',
                City::find($item->city_id)->name??'',
                Subcategory::find($item->subcategory_id)->name??'',
                $item->stars,
                Organization::find($item->organization_id)->name??'',

            ];
        }

        return new Collection([
            ['ID', 'site', 'title', 'about', 'Category', 'City', 'Subcategory', 'stars', 'organization'],
            $order
        ]);
    }
}
