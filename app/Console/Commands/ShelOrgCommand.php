<?php

namespace App\Console\Commands;

use App\Http\Controllers\JobsController;
use App\Http\Controllers\UsersController;
use App\Role;
use App\User;
use Illuminate\Console\Command;
use App\Traits\GlobalLines;
use Goutte\Client;
use Illuminate\Support\Facades\Lang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ShelOrgCommand extends Command
{
    use GlobalLines;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scraping:shel:org';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';
    protected $mainUrl = 'http://www.shel.org.il/';
    protected $areasUrl = 'http://www.shel.org.il/%D7%9E%D7%A7%D7%95%D7%9E%D7%95%D7%AA-%D7%9C%D7%A9%D7%A0%D7%94-%D7%94%D7%91%D7%90%D7%94';
    protected $client;
    protected $data = [
        'site' => 'http://www.shel.org.il/',
        'url' => 'http://www.shel.org.il/',
        'organization_id' => null,
        'hr_id' => null,
        'city_id' => null,
        'title' => null,
        'category_id' => null,
        'subcategory_id' => null,
        'images' => [],
        'about' => '',
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
        $crawler = $this->client->request('GET', $this->areasUrl);
        preg_match('!\d+!', str_replace('-','',$crawler->filter('.phn_sec strong')->text()), $phone);
        $request = new Request();
        $userController = new UsersController();
        $organization = $this->_workingWithSimpleTable('App\Organization', ['website' => $this->data['site']]);
        $this->data['organization_id'] = $organization->id;
        if (!$hr = User::where('phone', $phone)->first()) {
            $hr = $userController->store($request->merge([
                'phone' => $phone[0],
                'role_id' => Role::HR,
                'organization_id' => $this->data['organization_id'],
            ]), true);
        }
        $this->data['hr_id'] = $hr ? $hr->id : null;
        $crawler->filter('#Table_01 a')->each(function($areaNode){
            $areaName = $areaNode->filter('img')->attr('name');
            $data = $this->_workingWithParentAndChildTables($areaName, '', 'App\Area', 'App\City','area','city');
            $this->data['city_id'] = $data['city_id'];
            $categoriesCrawler = $this->client->request('GET', $this->mainUrl.$areaNode->attr('href'));
//            $title = $categoriesCrawler->filter('.head_sec h3')->text();
            $categoriesCrawler->filter('.left_sec p a')->each(function($categoriesNode){
                $categoryName = $categoriesNode->text();
                $data = $this->_workingWithParentAndChildTables($categoryName, '', 'App\Category', 'App\Subcategory','category','subcategory');
                $this->data['category_id'] = $data['category_id'];
                $this->data['subcategory_id'] = $data['subcategory_id'];
                $jobsCrawler = $this->client->request('GET', $this->mainUrl.$categoriesNode->attr('href'));
                $this->data['about'] = '';
                $jobsCrawler->filter('.listing ul li')->each(function($jobNode){
                    try {
                        $jobCrawler = $this->client->request('GET',$jobNode->filter('.button a')->attr('href'));
                    }catch (\Exception $e) {
                        return;
                    }
                    if(!$jobCrawler->filter('.head_sec h3')->count()) {
                        return;
                    }
                    $this->data['title'] = $jobCrawler->filter('.head_sec h3')->text();
                    $img = $jobCrawler->filter('.image_sec img')->count() ? $jobCrawler->filter('.image_sec img')->attr('src') : '';
                    if(!empty($img)) {
                        try {
                            $imageLink = file_get_contents($this->mainUrl.$img);
                            $image = time() . '.png';
                            $this->data['images'] = [$image];
                            $new = storage_path('app/public/jobs/') . '/' . $image;
                            file_put_contents($new, $imageLink);
                        } catch (\Exception $e) {

                        }
                    }
                    $jobCrawler->filter('.left_sec p')->each(function($aboutNode){
                       $this->data['about'] .= "<p>".$aboutNode->text()."</p>";
                    });
                    try {
                        $this->data['url'] = $jobNode->filter('.button a')->attr('href');
                        $request = new Request();
                        $jobController = new JobsController();
                        $job = $jobController->store($request->merge($this->data), true);
                    } catch (\Exception $e) {
                        dd($e->getMessage());
                        Log::info($e->getMessage());
                    }
                });
            });
        });
    }
}
