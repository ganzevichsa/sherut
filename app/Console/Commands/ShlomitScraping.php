<?php

namespace App\Console\Commands;

use App\Http\Controllers\JobsController;
use App\Http\Controllers\UsersController;
use App\Role;
use App\Subcategory;
use App\Traits\GlobalLines;
use App\User;
use Illuminate\Console\Command;
use Goutte\Client;
use Illuminate\Support\Facades\Lang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ShlomitScraping extends Command
{
    use GlobalLines;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scraping:shlomit';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';
    protected $mainUrl = 'https://shlomit.org.il/';
    protected $currentUrl = 'https://shlomit.org.il/%D7%97%D7%99%D7%A4%D7%95%D7%A9-%D7%9E%D7%A7%D7%95%D7%9E%D7%95%D7%AA-%D7%A9%D7%99%D7%A8%D7%95%D7%AA/';
    protected $client;
    protected $urlsData = [
        'https://shlomit.org.il/%D7%97%D7%99%D7%A4%D7%95%D7%A9-%D7%9E%D7%A7%D7%95%D7%9E%D7%95%D7%AA-%D7%A9%D7%99%D7%A8%D7%95%D7%AA/?q_accommodationonsite=1' => 'כולל דיור',
        'https://shlomit.org.il/%d7%97%d7%99%d7%a4%d7%95%d7%a9-%d7%9e%d7%a7%d7%95%d7%9e%d7%95%d7%aa-%d7%a9%d7%99%d7%a8%d7%95%d7%aa/?srch=&is_shilat=1' => ' שיל"ת-שירות לאומי תורני'
    ];
    protected $data = [
        'site' => 'https://shlomit.org.il/',
        'url' => null,
        'title' => null,
        'city_id' => null,
        'category_id' => null,
        'subcategory_id' => null,
        'about' => null,
        'organization_id' => null,
        'hr_id' => null,
        'out_identificator' => null,
    ];
    protected $ignoreText = [
        'X סגור'
    ];

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
        $crawler = $this->client->request('GET', $this->currentUrl);
        $this->mainBlock($crawler, $this->currentUrl);
        $crawler->filter('.pagination a')->each(function ($jobsNode) {
            $crawler = $this->client->request('GET', $this->currentUrl . $jobsNode->attr('href'));
            $this->mainBlock($crawler, $this->currentUrl . $jobsNode->attr('href'));
        });
    }

    private function mainBlock($jobsNode, $currentUrl)
    {
        foreach ($this->urlsData as $url => $organizationName) {
            $organization = $this->_workingWithSimpleTable('App\Organization', ['website' => $this->data['site']]);
            $this->data['organization_id'] = $organization->id;
            $jobsNode->filter('#content div article')->each(function ($jobNode) use ($currentUrl) {
                $this->data['about'] = '';
                $this->data['out_identificator'] = $jobNode->attr('id') . '-shlomit';
                $this->data['title'] = $jobNode->filter('h3.result')->text();
                $areaName = $jobNode->filter('.wrap-pro span')->eq(0)->filter('a') ? $jobNode->filter('.wrap-pro span')->eq(0)->filter('a')->text() : '';
                $cityName = $jobNode->filter('.wrap-pro span')->eq(1)->filter('a') ? $jobNode->filter('.wrap-pro span')->eq(1)->filter('a')->text() : '';
                $data = $this->_workingWithParentAndChildTables($areaName, $cityName, 'App\Area', 'App\City', 'area', 'city');
//            $this->data['area_id'] = $data['area_id'];
                $this->data['city_id'] = $data['city_id'];
                
                /*
                $categoryName = $jobNode->filter('.wrap-pro span')->eq(2)->filter('a') ? $jobNode->filter('.wrap-pro span')->eq(2)->filter('a')->text() : '';
                $subcategoryName = $jobNode->filter('.wrap-pro span')->eq(3)->filter('a') ? $jobNode->filter('.wrap-pro span')->eq(3)->filter('a')->text() : '';
                $data = $this->_workingWithParentAndChildTables($categoryName, $subcategoryName, 'App\Category', 'App\Subcategory', 'category', 'subcategory');
                $this->data['category_id'] = $data['category_id'];
                $this->data['subcategory_id'] = $data['subcategory_id'];
                */
                $jobNode->filter('.search_results_container p')->each(function ($descriptionNode) {
                    if (!in_array($descriptionNode->text(), $this->ignoreText)) {
                        $this->data['about'] .= "<p>" . $descriptionNode->text() . "</p>";
                    }
                });
                if ($hrBlock = $jobNode->filter('.search_results_container .p-details')->count()) {
                    try {
                        $hrText = $jobNode->filter('.search_results_container .p-details')->text();
                        $phoneText = $this->_getStringBetween($hrText, Lang::get('main.mobile_phone'), Lang::get('main.email'));
                        $phone = str_replace(':', '', $phoneText);
                        $hrPhone = trim(str_replace('-', '', $phone));
                        $nameText = $this->_getStringBetween($hrText, Lang::get('main.responsible_coordinator'), Lang::get('main.mobile_phone'));
                        $hrName = trim(str_replace(':', '', $nameText));
                        $request = new Request();
                        $userController = new UsersController();
                        if (!$hr = User::where('phone', $hrPhone)->first()) {
                            $hr = $userController->store($request->merge([
                                'name' => $hrName,
                                'phone' => $hrPhone,
                                'role_id' => Role::HR,
                                'organization_id' => $this->data['organization_id'],
                            ]), true);
                        }
                        $this->data['hr_id'] = $hr ? $hr->id : null;
                    } catch (\Exception $e) {

                    }
                }
                $this->data['url'] = $currentUrl;
                try {
                    $request = new Request();
                    $jobController = new JobsController();
                    $job = $jobController->store($request->merge($this->data), true);
                } catch (\Exception $e) {
                    dd($this->data);
                    Log::info($e->getMessage());
                }
            });
        }
    }
}
