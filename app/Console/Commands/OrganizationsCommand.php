<?php

namespace App\Console\Commands;

use App\Job;
use App\Organization;
use App\User;
use Illuminate\Console\Command;

class OrganizationsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:organizations';

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
        $organizations = Organization::all();
        foreach($organizations as $organization)
        {
            $managers = $organization->managers()->pluck('id');
            User::where('organization_id',$organization->id)->update(['organization_id' => null]);
            Job::where('organization_id',$organization->id)->update(['organization_id' => null]);
            Job::whereIn('manager_id',$managers)->update(['manager_id' => null]);
            $organization->managers()->delete();
            $organization->delete();
        }
    }
}
