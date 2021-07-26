<?php

namespace App\Console\Commands;

use App\Http\Controllers\JobsController;
use App\Http\Controllers\UsersController;
use App\JobType;
use App\Role;
use App\User;
use Illuminate\Console\Command;
use App\Traits\GlobalLines;
use Goutte\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;

class AminadavScraping extends Command
{
    use GlobalLines;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scraping:aminadav';

    protected $urls = [
        [
            'url' => 'https://aminadav.org.il/%D7%9E%D7%A7%D7%95%D7%9E%D7%95%D7%AA-%D7%A9%D7%99%D7%A8%D7%95%D7%AA/',
            'role' => Role::USER_BEFORE_SCHOOL_SECOND,
            'jobsNode' => '.posts-wrap .fl-post',
            'jobItemNode' => ''
        ],
        [
            'url' => 'https://aminadav.org.il/%D7%9E%D7%93%D7%A8%D7%A9%D7%95%D7%AA/',
            'role' => Role::USER_BEFORE_SCHOOL,
            'jobsNode' => '.fl-post-grid .fl-post-column',
            'jobItemNode' => '.fl-post-grid-post'
        ],
        [
            'url' => 'https://aminadav.org.il/%D7%92%D7%A8%D7%A2%D7%99%D7%A0%D7%99%D7%9D/',
            'role' => Role::USER_BEFORE_SCHOOL_SECOND,
            'jobsNode' => '.fl-post-grid .fl-post-column',
            'jobItemNode' => '.fl-post-grid-post'
        ]
    ];
    protected $client;
    protected $data = [
        'site' => 'https://aminadav.org.il',
        'url' => '',
        'title' => null,
        'city_id' => null,
        'address_id' => null,
        'about' => null,
        'description' => null,
        'manager_id' => null,
        'category_id' => null,
        'subcategory_id' => null,
        'out' => 0,
        'dormitory' => 0,
        'out_identificator' => 0,
        'organization_id' => null,
        'other_hr_name' => null,
        'other_hr_phone' => null,
    ];
    protected $localData = [
        'hrName' => null,
        'hrPhone' => null,
        'managerPhone' => null,
        'managerName' => null,
        'organizationWebsite' => null,
        'organizationEmail' => null,
        'jobTypeItemText' => null,
        'jobTypeId' => null,
        'categoryName' => null,
        'subCategoryName' => null,
        'places' => null,
        'secondHrName' => null,
        'secondHrPhone' => null,
    ];
    protected $hr = null;
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
        foreach ($this->urls as $urlData) {
            $crawler = $this->client->request('GET', $urlData['url']);
            $jobTypeItemText = $crawler->filter('h1.fl-heading')->text();
            $jobType = JobType::where('role_id', $urlData['role'])->first();
            $this->localData['jobTypeItemText'] = $jobTypeItemText;
            $this->localData['jobTypeId'] = $jobType->id;
            $this->data['job_type_id'] = $jobType->id;
            $crawler->filter($urlData['jobsNode'])->each(function ($jobNode) use($urlData) {
                preg_match('!post-\d+!', !empty($urlData['jobItemNode']) ? $jobNode->filter($urlData['jobItemNode'])->attr('class') : $jobNode->attr('class'), $code);
                $this->data['out_identificator'] = $code[0];

                $this->data['url'] = $jobNode->filter('.fl-post-title a')->attr('href');
                $jobCrawler = $this->client->request('GET', $this->data['url']);
                $organizationName = $jobCrawler->filter('#single-after-header h1.fl-heading')->text();
                $this->data['title'] = $organizationName;
                $this->data['title_old'] = $organizationName;
                $jobCrawler->filter('.fl-rich-text table tr')->each(function ($jobDetailsNode) use ($organizationName) {
                    if ($jobDetailsNode->attr('class') != 'hidden') {
                        if ($jobDetailsNode->filter('span[data-name="newdomain"]') && $jobDetailsNode->filter('span[data-name="newdomain"]')->count()) {
                            $categoryText = $jobDetailsNode->filter('span[data-name="newdomain"]')->text();
                            if ($jobDetailsNode->filter('i[class="amicon amicon-home"]')->count()) {
                                $this->localData['categoryName'] = $categoryText;
                            } else {
                                $this->localData['subCategoryName'] = $categoryText;
                            }
                        }
                        if ($jobDetailsNode->filter('span[data-name="city"]') && $jobDetailsNode->filter('span[data-name="city"]')->count()) {
                            $cityName = trim($jobDetailsNode->filter('span[data-name="city"]')->text());
                            if(!empty($cityName)) {
                                $city = $this->_workingWithSimpleTable('App\City', ['name' => $cityName]);
                                $this->data['city_id'] = $city->id;
                            }
                        }
                        if ($jobDetailsNode->filter('span[data-name="address"]') && $jobDetailsNode->filter('span[data-name="address"]')->count()) {
                            $addressName = trim($jobDetailsNode->filter('span[data-name="address"]')->text());
                            if(!empty($addressName)) {
                                $address = $this->_workingWithSimpleTable('App\Address', ['name' => $addressName]);
                                $this->data['address_id'] = $address->id;
                            }
                        }
                        if ($jobDetailsNode->filter('span[data-name="rakezet_name"]') && $jobDetailsNode->filter('span[data-name="rakezet_name"]')->count()) {
                            $this->localData['hrName'] = $jobDetailsNode->filter('span[data-name="rakezet_name"]')->text();
                        }
                        if ($jobDetailsNode->filter('span[data-name="rakezet_phone"]') && $jobDetailsNode->filter('span[data-name="rakezet_phone"]')->count()) {
                            $this->localData['hrPhone'] = str_replace('-', '', $jobDetailsNode->filter('span[data-name="rakezet_phone"]')->text());
                            $this->hr = User::where('phone', $this->localData['hrPhone'])->first();
                        }

                        if ($jobDetailsNode->filter('span[data-name="manager"]') && $jobDetailsNode->filter('span[data-name="manager"]')->count()) {
                            $managerDetails = trim($jobDetailsNode->filter('span[data-name="manager"]')->text());
                            preg_match_all('!\d+!', $managerDetails, $managerPhone);
                            if (is_array($managerPhone) && isset($managerPhone[0])) {
                                if (isset($managerPhone[0][0]) && isset($managerPhone[0][1])) {
                                    $this->localData['managerPhone'] = $managerPhone[0][0] . $managerPhone[0][1];
                                    $managerDetails = str_replace('-', '', $managerDetails);
                                    $managerDetails = str_replace($managerPhone[0][0], '', $managerDetails);
                                    $managerDetails = str_replace($managerPhone[0][1], '', $managerDetails);
                                }
                            }
                            $this->localData['managerName'] = trim($managerDetails);
                        }
                        if ($jobDetailsNode->filter('span[data-name="melave"]') && $jobDetailsNode->filter('span[data-name="melave"]')->count()) {
                            $this->localData['secondHrName'] = $jobDetailsNode->filter('span[data-name="melave"]')->text();
                            $this->data['other_hr_name'] = $jobDetailsNode->filter('span[data-name="melave"]')->text();
                        }
                        if ($jobDetailsNode->filter('span[data-name="melave_phone"]') && $jobDetailsNode->filter('span[data-name="melave_phone"]')->count()) {
                            $this->localData['secondHrPhone'] = $jobDetailsNode->filter('span[data-name="melave_phone"]')->text();
                            $this->data['other_hr_phone'] = $jobDetailsNode->filter('span[data-name="melave"]')->text();
                        }
                        if ($jobDetailsNode->filter('span[data-name="website"]') && $jobDetailsNode->filter('span[data-name="website"]')->count()) {
                            $this->localData['organizationWebsite'] = $jobDetailsNode->filter('span[data-name="website"]')->text();
                        }
                        if ($jobDetailsNode->filter('span[data-name="email"]') && $jobDetailsNode->filter('span[data-name="email"]')->count()) {
                            $this->localData['organizationEmail'] = $jobDetailsNode->filter('span[data-name="email"]')->text();
                        }
                        if ($jobDetailsNode->filter('span[data-name="total_type"]') && $jobDetailsNode->filter('span[data-name="total_type"]')->count()) {
                            $places = $jobDetailsNode->filter('span[data-name="total_type"]')->text();
                            $this->localData['places'] = $places;

                        }
                        if ($jobDetailsNode->filter('span[data-name="diour"]') && $jobDetailsNode->filter('span[data-name="diour"]')->count()) {
                            if (trim($jobDetailsNode->filter('span[data-name="diour"]')->text()) == Lang::get('main.yes')) {
                                $this->data['dormitory'] = $this->localData['places'];
                            } else {
                                $this->data['out'] = $this->localData['places'] ? $this->localData['places'] : 0;
                            }
                        }else {
                            $this->data['out'] = $this->localData['places'] ? $this->localData['places'] : 0;
                        }
                    }
                });

                if ($jobCrawler->filter('.til-field-wrap img')->count()) {
                    if($this->get_http_response_code($jobCrawler->filter('.til-field-wrap img')->attr('src')) != "200"){
                    }else{
                        // $imageLink = file_get_contents($jobCrawler->filter('.til-field-wrap img')->attr('src'));
                        // $image = time() . '.png';
                        // $new = storage_path('app/public/jobs') . '/' . $image;
                        // file_put_contents($new, $imageLink);
                        // $this->data['images'] = [$image];
                    }
                }
                $jobCrawler->filter('h3')->each(function ($descriptionNode) {
                    if (trim($descriptionNode->text()) == Lang::get('main.background')) {
                        $this->data['about'] = $descriptionNode->closest('.fl-rich-text')->filter('p')->text();
                    } else {
                        if (trim($descriptionNode->text()) == Lang::get('main.service_places_contained')) {
                            return;
                        }
                        if ($descriptionNode->closest('.fl-rich-text'))
                            $this->data['description'] .= '<h3>' . $descriptionNode->text() . '</h3>' . '<p>' . $descriptionNode->closest('.fl-rich-text')->filter('p')->text() . '</p>';
                    }
                });

                /* // Категории, добавление
                $category = $this->_workingWithSimpleTable('App\Category', ['name' => !empty($this->localData['categoryName']) ? $this->localData['categoryName'] : $this->localData['jobTypeItemText'], 'job_type_id' => $this->localData['jobTypeId']]);
                $subcategory = $this->_getDataFromTable('App\Subcategory', ['name' => !empty($this->localData['subCategoryName']) ? $this->localData['subCategoryName'] : Lang::get('main.general'), 'category_id' => $category->id]);
                if (!$category->subcategories()->where('name',!empty($this->localData['subCategoryName']) ? $this->localData['subCategoryName'] : Lang::get('main.general'))->count()) {
                    $subcategory = $this->_workingWithSimpleTable('App\Subcategory', ['name' => !empty($this->localData['subCategoryName']) ? $this->localData['subCategoryName'] : Lang::get('main.general'), 'category_id' => $category->id]);
                }

                */

                // $this->data['category_id'] = $category->id;
                // $this->data['subcategory_id'] = $subcategory->id;

//                $organization = $this->_workingWithSimpleTable('App\Organization', ['name' => $organizationName, 'website' => $this->localData['organizationWebsite'], 'email' => $this->localData['organizationEmail']]);
                $organization = $this->_workingWithSimpleTable('App\Organization', ['website' => $this->data['site']]);
                if ($this->localData['managerPhone'] || $this->localData['managerName']) {
                    $manager = $this->_workingWithSimpleTable('App\OrganizationManager', ['organization_id' => $organization->id, 'name' => $this->localData['managerName'], 'phone' => $this->localData['managerPhone']]);
                    $this->data['manager_id'] = $manager->id;
                }
                $request = new Request();
                $userController = new UsersController();
                if (!$this->hr) {
                    try {
                        $this->hr = $userController->store($request->merge([
                            'name' => $this->localData['hrName'] ? $this->localData['hrName'] : ($this->localData['secondHrName'] ? $this->localData['secondHrName'] : ''),
                            'phone' => $this->localData['hrName'] ? $this->localData['hrPhone'] : $this->localData['secondHrPhone'],
                            'role_id' => Role::HR,
                            'organization' => $organization->id,
                        ]), true);
                    } catch( \Exception $e){

                    }
                }
                $this->data['hr_id'] = $this->hr ? $this->hr->id : '';
                $this->data['organization_id'] = $organization->id;
                try {
                    if(!empty($this->data['title'])) {
                        $request = new Request();
                        $jobController = new JobsController();
                        $job = $jobController->store($request->merge($this->data), true);
                    }
                } catch (\Exception $e) {
                    dd($e->getMessage());
                    Log::info($e->getMessage());
                }
            });
        }

    }

    function get_http_response_code($url) {
        $headers = get_headers($url);
        return substr($headers[0], 9, 3);
    }
}
