<?php

use Illuminate\Database\Seeder;

class OrganizationManagersChangeOrganizationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $managers = \App\OrganizationManager::all();
        foreach($managers as $manager) {
            if(!$manager->organization->website) {
                $organization = \App\Organization::where('website',$manager->organization->name)->first();
                $manager->organization_id = $organization->id;
                $manager->save();
            }
        }
        $users = \App\User::all();
        foreach($users as $user) {
            if(!$user->organization) {
                continue;
            }
            if(!$user->organization->website) {
                $organization = \App\Organization::where('website',$user->organization->name)->first();
                $user->organization_id = $organization->id;
                $user->save();
            }
        }
    }
}
