<?php

namespace App\Console\Commands;

use App\Job;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ClearDuplicateJobs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clear:duplicate:jobs';

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
//        'SELECT * FROM (SELECT title, count(title) as count FROM `jobs` WHERE organization_id = 5 GROUP BY title) jobs where count > 1'
//        "DELETE FROM job_images where job_id IN (select id from jobs where title = 'גרעין ראשון לציון' and is_admin_update = 0 )"
//        "DELETE FROM `jobs` WHERE title = 'גרעין ראשון לציון' and is_admin_update = 0"
        $jobs = DB::select('SELECT * FROM (SELECT title, count(title) as count FROM `jobs` WHERE organization_id = 5 GROUP BY title) jobs where count > 1');
        foreach($jobs as $job) {
            if(!Job::where('title',$job->title)->where('is_admin_update',1)->count()) {
                $first_job_id = Job::where('title',$job->title)->first()->id;
                DB::select("DELETE FROM job_images where job_id IN (select id from jobs where title = '".$job->title."' and is_admin_update = 0 and id != ".$first_job_id." )");
                DB::select("DELETE FROM organization_routes_jobs where job_id IN (select id from jobs where title = '".$job->title."' and is_admin_update = 0 and id != ".$first_job_id.")");
                DB::select("DELETE FROM `jobs` WHERE title = ".$job->title." and is_admin_update = 0 and id != ".$first_job_id);
            } else {
                DB::select("DELETE FROM job_images where job_id IN (select id from jobs where title = '".$job->title."' and is_admin_update = 0 )");
                DB::select("DELETE FROM organization_routes_jobs where job_id IN (select id from jobs where title = '".$job->title."' and is_admin_update = 0 )");
                DB::select("DELETE FROM `jobs` WHERE title = '".$job->title."' and is_admin_update = 0");
            }
        }
    }
}
