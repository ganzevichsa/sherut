<?php

namespace App\Console\Commands;

use App\Http\Controllers\JobsController;
use App\Http\Controllers\UsersController;
use App\Job;
use App\Role;
use App\Traits\GlobalLines;
use App\User;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;

class BatamiScraping extends Command
{
    use GlobalLines;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scraping:batami';


    protected $main_url = [
        'http://api.bat-ami.org.il/Api/Search/SearchInstitutes?City=&Coo=&Seed=&Text=&Act=&accH=0&accO=0&accD=0&Year=1&Area=',
        'http://api.bat-ami.org.il/Api/Search/SearchInstitutes?City=&Coo=&Seed=&Text=&Act=&accH=0&accO=0&accD=0&Year=0&Area='
    ];
    protected $site_url = 'http://bat-ami.org.il/';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $organization = $this->_workingWithSimpleTable('App\Organization', ['website' => $this->site_url]);
        foreach($this->main_url as $main_url) {
            $json = file_get_contents($main_url);
            $jobsData = json_decode($json, true);

            foreach ($jobsData as $job) {
                if(!empty($job['CityName']))
                $city = $this->_workingWithSimpleTable('App\City', ['name' => $job['CityName']]);

                /*
                if(!empty($job['CategoryName']))
                $category = $this->_workingWithSimpleTable('App\Category', ['name' => $job['CategoryName']]);
                $subcategory = $this->_getDataFromTable('App\Subcategory', ['name' => Lang::get('main.general'), 'category_id' => $category->id]);
                if (!$category->subcategories()->where('name', Lang::get('main.general'))->count()) {
                    $subcategory = $this->_workingWithSimpleTable('App\Subcategory', ['name' => Lang::get('main.general'), 'category_id' => $category->id]);
                }

                */
                $userController = new UsersController;
                $request = new Request();
                if (!$hr = User::where('phone', $job['ContactPhone'])->first()) {
                    $hr = $userController->store($request->merge([
                        'name' => $job['ContactName'],
                        'phone' => $job['ContactPhone'],
                        'role_id' => Role::HR,
                        'organization_id' => $organization->id,
                    ]), true);
                }
                $imageLink = file_get_contents($job['ImageLink']);
                $image = time().'.png';
                $new = storage_path('app/public/jobs/').'/'.$image;
                file_put_contents($new, $imageLink);
                $jobController = new JobsController();
                $request = new Request();
                $data = [
                    'out_identificator' => $job["Code"],
                    'site' => $this->site_url,
                    'title' => $job['Name'],
                    'city_id' => $city->id,
                    // 'category_id' => $category->id,
                    // 'subcategory_id' => $subcategory->id,
                    'home' => $job['cHomePosts'],
                    'out' => $job['cOutPosts'],
                    'dormitory' => $job['cDormitoryPosts'],
                    // 'images' => [$image],
                    'hr_id' => $hr->id,
                    'organization_id' => $organization->id,
                ];
                try{
                    $job = $jobController->store($request->merge($data), true);
                }catch (\Exception $e) {
                    dd($e->getMessage());
                }
            }
        }
    }
}
