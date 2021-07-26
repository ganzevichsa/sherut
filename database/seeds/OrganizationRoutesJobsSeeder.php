<?php

use Illuminate\Database\Seeder;

class OrganizationRoutesJobsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach(\App\Job::all() as $job) {
            if($job->organization_id) {
                $route = \App\OrganizationRoute::where('organization_id',$job->organization_id)->first();
                if($route) {
                    $job->organizationRoute()->sync($route->id);
                }
            }
        }
    }
}
