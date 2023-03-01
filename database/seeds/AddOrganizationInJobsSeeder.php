<?php

use Illuminate\Database\Seeder;

class AddOrganizationInJobsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $jobs = \App\Job::whereNull('organization_id')->get();
        foreach($jobs as $job) {
            $organization = \App\Organization::where('website',$job->site)->first();
            if($organization) {
                $job->organization_id = $organization->id;
                $job->save();
            }
        }
        $jobs = \App\Job::all();
        foreach($jobs as $job) {
            if(empty($job->organization->website)) {
                $organization = \App\Organization::where('website',$job->site)->first();
                if($organization) {
                    $job->organization_id = $organization->id;
                    $job->save();
                }
            }
        }

        $organizationManagers = \App\OrganizationManager::all();
        foreach($organizationManagers as $manager) {
            if(empty($manager->organization->website)) {
                $organization = \App\Organization::where('website',$organization->site)->first();
                if($organization) {
                    $organization->organization_id = $organization->id;
                    $organization->save();
                }
            }
        }
    }
}
