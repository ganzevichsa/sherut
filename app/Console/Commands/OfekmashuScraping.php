<?php

namespace App\Console\Commands;

use App\Http\Controllers\JobsController;
use App\Traits\GlobalLines;
use Goutte\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Lang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OfekmashuScraping extends Command
{
    use GlobalLines;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scraping:ofekmashu';


    protected $main_url = 'https://ofekmashu.org.il';
    protected $url = 'https://ofekmashu.org.il/tkanim/';
    protected $currentUrl = 'https://ofekmashu.org.il/tkanim/';
    protected $client;
    protected $data = [
        'site' => 'https://ofekmashu.org.il',
        'category_id' => 0,
        'stage_of_education_id' => 0,
        'city_id' => 0,
        'address_id' => 0,
        'title' => '',
        'out_identificator' => null,
        'organization_id' => null,
    ];

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';
    protected $organization;

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
        $crawler = $this->client->request('GET', $this->url);
        $this->organization = $this->_workingWithSimpleTable('App\Organization', ['website' => $this->data['site']]);
        $this->data['organization_id'] = $this->organization->id;
        $crawler->filter('div[data-elementor-type="wp-page"] .elementor-button-link')->each(function ($node) {
            if (!$node->filter('.elementor-button-icon')->count()) {
//                sleep(5);
                $this->currentUrl = $this->main_url . $node->attr('href');
                if (preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i",$node->attr('href'))) {
                    $this->currentUrl = $node->attr('href');
                }

                $page = $this->client->request('GET', $this->currentUrl);
                $page->filter('.ee-loop .ee-loop__item')->each(function ($pageNode) {
                    preg_match('!post-\d+!', $pageNode->filter('article')->attr('class'), $code);
                    $this->data['out_identificator'] = $code[0];
                    $this->data['title'] = $pageNode->filter('.elementor-widget-wrap div[data-widget_type="heading.default"] h2 a')->text();
                    $pageNode->filter('.elementor-widget-wrap div[data-widget_type="text-editor.default"]')->each(function ($jobNode) {
                        $jobTextNode = trim(strtolower($jobNode->filter('.elementor-text-editor')->text()));
                        if (!empty($jobTextNode)) {
                            
                            /*
                            if (stripos($jobTextNode, strtolower(Lang::get('main.category')) . ":") !== false) {
                                $category = trim(str_replace(Lang::get('main.category') . ":", '', $jobTextNode));
                                if (!empty($category)) {
                                    $category = $this->_workingWithSimpleTable('App\Category', ['name' => $category]);
                                    $subcategory = $this->_getDataFromTable('App\Subcategory', ['name' => Lang::get('main.general'), 'category_id' => $category->id]);
                                    if (!$category->subcategories()->where('name', Lang::get('main.general'))->count()) {
                                        $subcategory = $this->_workingWithSimpleTable('App\Subcategory', ['name' => Lang::get('main.general'), 'category_id' => $category->id]);
                                    }
                                    $this->data['category_id'] = $category->id;
                                    $this->data['subcategory_id'] = $subcategory->id;
                                }
                            }
                            */


                            if (stripos($jobTextNode, strtolower(Lang::get('main.stages_of_education')) . ":") !== false) {
                                $stages_of_education = trim(str_replace(Lang::get('main.stages_of_education') . ":", '', $jobTextNode));
                                $this->data['stage_of_education_id'] = $this->_workingWithSimpleTable('App\StageOfEducation', ['name' => $stages_of_education])->id;
                            }
                            if (stripos($jobTextNode, strtolower(Lang::get('main.location')) . ":") !== false) {
                                $location = trim(str_replace(Lang::get('main.location') . ":", '', $jobTextNode));
                                $this->data['city_id'] = $this->_workingWithSimpleTable('App\City', ['name' => $location])->id;
                            }
                            if (stripos($jobTextNode, strtolower(Lang::get('main.address')) . ":") !== false) {
                                $address = trim(str_replace(Lang::get('main.address') . ":", '', $jobTextNode));
                                $this->data['address_id'] = $this->_workingWithSimpleTable('App\Address', ['name' => $address])->id;
                            }
                            if ($jobNode->filter('p')->count()) {
                                $this->data['about'] = $jobNode->filter('p')->text();
                            }
                            $this->data['url'] = $this->currentUrl;
                        }
                    });
                    $request = new Request();
                    $jobsController = new JobsController;
                    try {
                        $jobsController->store($request->merge($this->data), true);
                    } catch (\Exception $e) {

                    }
                });
            }
        });
    }
}
