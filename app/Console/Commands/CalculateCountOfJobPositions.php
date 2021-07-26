<?php

namespace App\Console\Commands;

use App\Job;
use Illuminate\Console\Command;

class CalculateCountOfJobPositions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'calculate:jobs:positions';

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
        $jobs = Job::all();
        foreach($jobs as $job) {
            $job->count_of_all_positions = $job->out + $job->home + $job->dormitory;
            $job->stars = $job->reviews()->count() ? $job->reviews()->sum('stars') : 0;
            $job->save();
        }
    }
}
