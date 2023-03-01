<?php

namespace App\Console\Commands;

use App\Http\Controllers\JobsController;
use App\Http\Controllers\UsersController;
use App\Job;
use App\Role;
use App\User;
use Illuminate\Console\Command;
use App\Traits\GlobalLines;
use Goutte\Client;
use Illuminate\Support\Facades\Lang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class HiburhadashCommand extends Command
{
    use GlobalLines;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scraping:hiburhadash';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    protected $main_url = 'http://hibur-hadash.org.il/position/';
    protected $data = [
        'site' => 'http://hibur-hadash.org.il',
        'url' => 'http://hibur-hadash.org.il/position/',
        'title' => '',
        'about' => '',
        'category_id' => null,
        'subcategory_id' => null,
        'city_id' => null,
        'hr_id' => null,
        'organization_id' => null,
    ];

    protected $increment = 0;
    protected $organization = 0;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->client = new Client();
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->organization = $this->_workingWithSimpleTable('App\Organization', ['website' => $this->data['site']]);
        $this->data['organization_id'] = $this->organization->id;
        $crawler = $this->client->request('GET', $this->main_url);
        $crawler->filter('.one-service')->each(function ($categoryNode) {
            if ($categoryName = $this->_getCategoryFromTranslations($categoryNode->filter('.service-title h2')->text())) {
                $category = $this->_workingWithSimpleTable('App\Category', ['name' => $categoryName]);
                if (!empty($category)) {
                    $subcategory = $this->_getDataFromTable('App\Subcategory', ['name' => Lang::get('main.general'), 'category_id' => $category->id]);
                    if (!$category->subcategories()->where('name', Lang::get('main.general'))->count()) {
                        $subcategory = $this->_workingWithSimpleTable('App\Subcategory', ['name' => Lang::get('main.general'), 'category_id' => $category->id]);
                    }
                    $this->data['category_id'] = $category->id;
                    $this->data['subcategory_id'] = $subcategory->id;
                }
            } else {
//                dd('Category do not find');
            }
            $categoryNode->filter('.all-positions-in-service .col-md-6')->each(function ($jobsNode) {
                if(Job::where('site',$this->data['site'])->where('title',$this->data['title'])->first()) {
                    return;
                }
                $this->data['title'] = $jobsNode->filter('h3')->text();
                $this->data['about'] = $jobsNode->filter('.disclaimer')->count() ? $jobsNode->filter('.disclaimer')->text() : '';
                $cityName = trim($jobsNode->filter('div.text')->eq(1)->text());
                $city = $this->_workingWithSimpleTable('App\City', ['name' => $cityName]);
                $this->data['city_id'] = $city->id;
                if ($jobsNode->filter('.position-link')->count()) {
                    $imageLink = file_get_contents($jobsNode->filter('.position-link a')->attr('href'));
                    $image = time().'.png';
                    $new = storage_path('app/public/jobs/').'/'.$image;
                    file_put_contents($new, $imageLink);
                    $this->data['images'] = [$image];
                }
                $hrName = '';
                $hrPhone = '';
                try {
                    $hrNode = $jobsNode->filter('div.text');
                    if($hrNode->eq(2)) {
                        $hrName = trim($jobsNode->filter('div.text')->eq(2)->text());
                    }
                    if($hrNode->eq(3)) {
                        $hrPhone = trim(explode(':', $jobsNode->filter('div.text')->eq(3)->text())[1]);
                        $hrPhone = str_replace('-','',$hrPhone);
                    }
                }catch(\Exception $e) {
                }
                $request = new Request();
                $userController = new UsersController();
                if (!$hr = User::where('phone', $hrPhone)->first()) {
                    try {
                        $hr = $userController->store($request->merge([
                            'name' => $hrName,
                            'phone' => $hrPhone,
                            'role_id' => Role::HR,
                            'organization_id' => $this->organization->id,
                        ]), true);
                    } catch(\Exception $e) {

                    }
                }
                $this->data['hr_id'] = $hr ? [$hr->id] : null;
                try {
                    $request = new Request();
                    $jobController = new JobsController();
                    $job = $jobController->store($request->merge($this->data), true);
                } catch (\Exception $e) {
                    dd($this->data);
                    Log::info($e->getMessage());
                }
                $this->increment++;
            });
        });
        return 0;
    }
}
