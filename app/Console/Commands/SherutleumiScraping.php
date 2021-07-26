<?php

namespace App\Console\Commands;

use App\Http\Controllers\JobsController;
use App\Http\Controllers\UsersController;
use App\Job;
use App\Role;
use App\Traits\GlobalLines;
use App\User;
use Illuminate\Console\Command;
use Goutte\Client;
use Illuminate\Support\Facades\Lang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SherutleumiScraping extends Command
{
    use GlobalLines;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scraping:sherutleumi';

    protected $main_url = 'https://sherut-leumi.co.il/datiot/places.aspx';
    protected $url = 'https://sherut-leumi.co.il/datiot/';
    protected $current_url = 'places.aspx?page=';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    protected $images;
    protected $hr_jobs;
    protected $pages_count;

    protected $data = [
        'site' => 'https://sherut-leumi.co.il',
        'city_id' => null,
        'category_id' => null,
        'subcategory_id' => null,
        'home' => null,
        'out' => null,
        'hr_id' => null,
        'title' => '',
        'about' => '',
        'description' => '',
        'images' => [],
        'out_identificator' => null,
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
        $crawler = $this->client->request('GET', $this->main_url);
        preg_match('!\d+!', $crawler->filter('.pager a.last')->attr('href'), $pagesCount);
        $this->pages_count = $pagesCount;
        for($page = 1; $page <= $this->pages_count; $page++){
            $href = $this->current_url.$page;
            $this->data['url'] = $this->url . $href;
            $pageCrawler = $this->client->request('GET', $this->data['url']);
            $pageCrawler->filter('.list_hold ul.list1 li')->each(function ($tableRowNode) {
                $organization = null;

                if ($tableRowNode->filter('.box2')->count()) {
//                    $organizationName = $tableRowNode->filter('.box2 p')->eq(0)->text();
                    $organization = $this->_workingWithSimpleTable('App\Organization', ['website' => $this->data['site']]);

                    $areaName = $tableRowNode->filter('.box2 p')->eq(2)->text();
                    $area = $this->_workingWithSimpleTable('App\Area', ['name' => $areaName]);

                    $cityName = $tableRowNode->filter('.box2 p')->eq(1)->text();
                    $city = $this->_workingWithSimpleTable('App\City', ['name' => $cityName, 'area_id' => $area->id]);
                    $this->data['city_id'] = $city->id;
                } else {
                    return;
                }

                /*
                if ($tableRowNode->filter('.box3')->count()) {
                    $text = $tableRowNode->filter('.box3')->text();
                    if ($categoryName = $this->_getCategoryFromTranslations($text)) {
                        $category = $this->_workingWithSimpleTable('App\Category', ['name' => $categoryName]);
                        $this->data['category_id'] = $category->id;

                        $subCategoryName = trim(str_replace($categoryName, '', $text));
                        $subcategory = $this->_workingWithSimpleTable('App\Subcategory', ['name' => $subCategoryName, 'category_id' => $category->id]);
                        $this->data['subcategory_id'] = $subcategory->id;
                    } else {
                        $fullJobUrl = $tableRowNode->filter('.box6 a')->attr('href');
                        preg_match('!\d+!', $fullJobUrl, $code);
//                        dd($code[0]);
//                        // TEST FOR OTHER CATEGORIES
//                        dd('find a new category');
                    }
                }*/

                if ($tableRowNode->filter('.box4')->count()) {
                    preg_match('!\d+!', $tableRowNode->filter('.box4')->text(), $home);
                    $this->data['home'] = $home[0];
                }
                if ($tableRowNode->filter('.box5')->count()) {
                    preg_match('!\d+!', $tableRowNode->filter('.box5')->text(), $out);
                    $this->data['out'] = $out[0];
                }
                if ($tableRowNode->filter('.box6')->count()) {
                    $fullJobUrl = $tableRowNode->filter('.box6 a')->attr('href');
                    preg_match('!\d+!', $fullJobUrl, $code);
                    $this->data['out_identificator'] = $code[0];
                    $this->images = [];
                    $fullJobCrawler = $this->client->request('GET', $this->url . $fullJobUrl);
                    $jobTitle = $fullJobCrawler->filter('.page_title')->text();
                    $jobAbout = $fullJobCrawler->filter('.info_box .text')->text();
                    $jobDescription = '';
                    if($fullJobCrawler->filter('.boxesLeft .box')->count()){
                        if($fullJobCrawler->filter('.boxesLeft .box')->eq(0)->filter('.in')->count()){
                            $jobDescription = $fullJobCrawler->filter('.boxesLeft .box')->eq(0)->filter('.in')->text();
                        }
                    }
                    $fullJobCrawler->filter('#gallery img')->each(function ($imageNode) {
                        $imageLink = file_get_contents($imageNode->attr('src'));
                        $image = time() . '.png';
                        $this->images[] = $image;
                        $new = storage_path('app/public/jobs/') . '/' . $image;
                        file_put_contents($new, $imageLink);
                    });
                    $this->data['title'] = $jobTitle;
                    $this->data['about'] = $jobAbout;
                    $this->data['description'] = $jobDescription;
                    $this->data['images'] = $this->images;
                }

                $hr = null;
                if ($tableRowNode->filter('.box7')->count()) {
                    $hrName = $tableRowNode->filter('.box7 a')->text();
                    $hrPhone = str_replace('-','',trim(str_replace($hrName, '', $tableRowNode->filter('.box7')->text())));
                    $request = new Request();
                    $userController = new UsersController();
                    try {
                        if (!$hr = User::where('phone', $hrPhone)->first()) {
                            $hr = $userController->store($request->merge([
                                'name' => $hrName,
                                'phone' => $hrPhone,
                                'role_id' => Role::HR,
                                'organization_id' => $organization ? $organization->id : null,
                            ]), true);
                        }
                    } catch(\Exception $e) {

                    }
                    $this->data['images'] = $this->images;
                    $this->data['hr_id'] = $hr->id;
                }
                try {
                    $request = new Request();
                    $jobController = new JobsController();
                    $job = $jobController->store($request->merge($this->data), true);
                } catch (\Exception $e) {
                    Log::info($e->getMessage());
                }
            });
        }
    }


}
