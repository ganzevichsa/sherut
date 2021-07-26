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
use Illuminate\Support\Facades\Lang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\DomCrawler\Crawler;


class MidrashotCommand extends Command
{
    use GlobalLines;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scraping:midrashot';
    protected $mainUrl = 'https://midrashot.co.il/';
    protected $client;
    protected $jobsAjaxUrl = 'https://midrashot.co.il/?mylisting-ajax=1&action=get_listings&security=f06d0bf646&form_data%5Bcontext%5D=term-search&form_data%5Btaxonomy%5D=case27_job_listing_tags&form_data%5Bterm%5D=91&form_data%5Bsort%5D=random&listing_type=place&listing_wrap=col-md-12+grid-item&form_data%5Bpage%5D';
    protected $ajaxCode = '';
    protected $ajaxTermID = '';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';
    protected $data = [
        'site' => 'https://midrashot.co.il/',
        'url' => null,
        'title' => null,
        'description' => null,
        'city_id' => null,
        'category_id' => null,
        'subcategory_id' => null,
        'about' => null,
        'organization_id' => null,
        'hr_id' => null,
        'out_identificator' => null,
        'images' => [],
        'video_url' => '',
        'doc_urls' => [],
        'job_type_id' => '',
        'midrasha_info' => [
            'categories' => [],
            'times' => [],
            'times_timezone' => '',
            'main_info' => [],
            'areas' => '',
            'terms' => [],
        ],
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
        $crawler = $this->client->request('GET', $this->mainUrl);
//        $organizationName = 'מדרשה';
        $organization = $this->_workingWithSimpleTable('App\Organization', ['website' => $this->data['site']]);
        $this->data['organization_id'] = $organization->id;
        $this->data['job_type_id'] = JobType::MIDRASHA;
        $crawler->filter('.all-categories .ac-category a')->each(function ($categoryNode) {
            $categoryName = trim($categoryNode->filter('.category-name')->text());
            $data = $this->_workingWithParentAndChildTables($categoryName, '', 'App\Category', 'App\Subcategory','category','subcategory');
            $this->data['category_id'] = $data['category_id'];
            $this->data['subcategory_id'] = $data['subcategory_id'];
            $jobsUrl = $categoryNode->attr('href');
            $jobsCrawler = $this->client->request('GET', $jobsUrl);
            $jobsCrawler->filter('script')->each(function($scriptNode) {
                if(!empty($scriptNode->text())) {
                    preg_match_all('/"ajax_nonce":"(.*?)"/',$scriptNode->text(),$output);
                    preg_match_all('/"activeTermId":(.*?),/',$scriptNode->text(),$termID);

                    if(count($output) == 2) {
                        if(!empty($output[1])) {
                            $this->ajaxCode = $output[1][0];
                        }
                    }
                    if(count($termID) == 2) {
                        if(!empty($termID[1])) {
                            foreach($termID[1] as $termIDS) {
                                if($termIDS) {
                                    $this->ajaxTermID = $termIDS;
                                }
                            }
                        }
                    }
                }
            });
            $json = file_get_contents("https://midrashot.co.il/?mylisting-ajax=1&action=get_listings&security=".$this->ajaxCode."&form_data%5Bcontext%5D=term-search&form_data%5Btaxonomy%5D=case27_job_listing_tags&form_data%5Bterm%5D=".$this->ajaxTermID ."&form_data%5Bsort%5D=random&listing_type=place&listing_wrap=col-md-12+grid-item&form_data%5Bpage%5D=0");
            $jobsData = json_decode($json, true);
            $numOfPages = $jobsData['max_num_pages'] - 1;
            $this->_mainBlock($jobsData);
            for($i = 1; $i <= $numOfPages; $i++) {
                $json = file_get_contents("https://midrashot.co.il/?mylisting-ajax=1&action=get_listings&security=".$this->ajaxCode."&form_data%5Bcontext%5D=term-search&form_data%5Btaxonomy%5D=case27_job_listing_tags&form_data%5Bterm%5D=".$this->ajaxTermID ."&form_data%5Bsort%5D=random&listing_type=place&listing_wrap=col-md-12+grid-item&form_data%5Bpage%5D=".$i);
                $jobsData = json_decode($json, true);
                $this->_mainBlock($jobsData);
            }
        });
    }

    private function _mainBlock($jobsData)
    {
        $crawler = new Crawler($jobsData['html']);
        $crawler->filter('.grid-item')->each(function($jobNode){
            $this->data['midrasha_info']['categories'] = [];
            $this->data['midrasha_info']['times'] = [];
            $this->data['midrasha_info']['times_timezone'] = '';
            $this->data['midrasha_info']['main_info'] = [];
            $this->data['midrasha_info']['terms'] = [];
            preg_match('!post-\d+!', $jobNode->filter('.listing-preview')->attr('class'), $code);
            $this->data['out_identificator'] = $code[0].'-midrashot';

            $crawler = $this->client->request('GET', $jobNode->filter('a')->eq(0)->attr('href'));
            $this->data['title'] = $crawler->filter('h1')->text();
            if($crawler->filter('.profile-cover-image')->count()){
                preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i",$crawler->filter('.profile-cover-image')->attr('style'), $image);
                if(!empty($image) && is_array($image)) {
                    $image = $this->_uploadImage($image[0]);
                    if(!empty($image))
                    $this->data['images'][] = $image;
                }
            }
            $this->data['title'] = $crawler->filter('h1')->text();
            $this->data['description'] = $crawler->filter('h1')->closest('.profile-name')->filter('h2')->text();
            $hrPhone = '';
            if($crawler->filter('.icon-phone-outgoing')->count()) {
                $hrPhone = str_replace('-','',$crawler->filter('.icon-phone-outgoing')->closest('li')->filter('span')->text());
            }
            if($crawler->filter('.icon-location-pin-add-2')->count()) {
                if($crawler->filter('.icon-location-pin-add-2')->closest('li')->filter('span')->count()) {
                    $cityName = $crawler->filter('.icon-location-pin-add-2')->closest('li')->filter('span')->text();
                    if(!empty($cityName)) {
                        $city = $this->_workingWithSimpleTable('App\City', ['name' => $cityName]);
                        $this->data['city_id'] = $city->id;
                    }
                }
            }
            if($crawler->filter('.pf-body .photoswipe-gallery .photoswipe-item')->count()) {
                $crawler->filter('.pf-body .photoswipe-gallery .photoswipe-item')->each(function($imagesNode){
                    $image = $this->_uploadImage($imagesNode->attr('href'));
                    $this->data['images'][] = $image;
                });
            }
            if($crawler->filter('.video-block-body iframe')->count()){
                $this->data['video_url'] = $crawler->filter('.video-block-body iframe')->attr('src');
            }
            if($crawler->filter('.block-type-categories li')->count()) {
                $crawler->filter('.block-type-categories li')->each(function($categoriesNode){
                     $this->data['midrasha_info']['categories'][] = $categoriesNode->filter('.category-name')->text();
                });
            }
            if($crawler->filter('#open-hours li')->count()) {
                $crawler->filter('#open-hours li')->each(function($timesNode) {
                    $this->data['midrasha_info']['times'][] = $timesNode->filter('.item-attr')->text().": ".$timesNode->filter('.item-property')->text();
                });
                $this->data['midrasha_info']['times_timezone'] = $crawler->filter('#open-hours .work-hours-timezone em')->text();
            }
            if($crawler->filter('.files-block a')->count()) {
                $crawler->filter('.files-block a')->each(function($filesNode){
                    $this->data['doc_urls'][] = $filesNode->attr('href');
                });
            }
            if($crawler->filter('.extra-details li')->count()) {
                $crawler->filter('.extra-details li')->each(function($extraNode) {
                    $this->data['midrasha_info']['main_info'][] = $extraNode->filter('.item-attr')->text().": ".$extraNode->filter('.item-property')->text();
                });
            }
            $this->data['about'] = $crawler->filter('.block-field-job_description #panel1')->count() ? $crawler->filter('.block-field-job_description #panel1')->html() : $crawler->filter('.block-field-job_description .pf-body')->html();
            $this->data['midrasha_info']['areas'] = $crawler->filter('.block-field-subjects .body')->count() ? $crawler->filter('.block-field-subjects .body')->html() : $crawler->filter('.block-field-subjects .pf-body')->html('');
            if($crawler->filter('.block-type-terms li')->count()) {
                $crawler->filter('.block-type-terms li')->each(function($termsNode){
                    $this->data['midrasha_info']['terms'][] = $termsNode->filter('.category-name')->text();
                });
            }

            $request = new Request();
            $userController = new UsersController();
            $hr = null;
            try {
                $hr = $userController->store($request->merge([
                    'phone' => $hrPhone,
                    'role_id' => Role::HR,
                    'organization' => $this->data['organization_id'],
                ]), true);
            } catch( \Exception $e){

            }
            try {
                $this->data['hr_id'] = $hr ? $hr->id : null;
                $this->data['url'] = $jobNode->filter('a')->eq(0)->attr('href');
                $request = new Request();
                $jobController = new JobsController();
                $job = $jobController->store($request->merge($this->data), true);
            } catch (\Exception $e) {
                dd($e->getMessage());
                Log::info($e->getMessage());
            }
        });
    }
}
