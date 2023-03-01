<?php

namespace App\Http\Controllers;

use App\Job;
use App\Organization;
use App\OrganizationManager;
use App\OrganizationImage;
use App\Traits\GlobalLines;
use App\User;
use Illuminate\Http\Request;
use App\Http\Resources\OrganizationsResource;

class OrganizationsController extends Controller
{
    use GlobalLines;

    public function index()
    {
        $organizations = Organization::all();
        return view('organizations.index', compact('organizations'));
    }

    public function create()
    {
        return view('organizations.create');
    }

    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|unique:organizations,name',
        ];
        $this->validate($request, $rules);
        $data = $request->all();
        $this->_mainControlBlock($data);
        return redirect(route('organizations.index'))->with('message', 'Organization successfully created');
    }

    public function edit($id)
    {
        if(!$organization = Organization::find($id)) {
            abort(404);
        }
        return view('organizations.edit',compact('organization'));
    }

    public function show($id)
    {
        if(!$organization = Organization::find($id)) {
            return response()->json(['organization_not_found'], 404);
        }

        return response()->json(new OrganizationsResource($organization));
    }

    public function update(Request $request, $id)
    {
        $rules = [
            'name' => 'required|unique:organizations,name,'.$id,
        ];
        $this->validate($request, $rules);
        $data = $request->all();
        if(!$organization = Organization::find($id)) {
            abort(404);
        }
        $this->_mainControlBlock($data, $organization);
        return redirect(route('organizations.index'))->with('message', 'Organization successfully updated');
    }

    public function getHr(Request $request)
    {
        $rules = [
            'id' => 'required|numeric',
        ];
        $this->validate($request, $rules);
        $data = $request->all();
        $hrs = User::where('organization_id',$data['id'])->get();
        return response()->json($hrs);
    }

    private function _mainControlBlock($data, $organization = null)
    {
        if(!$organization) {
            $organization = new Organization();
        }
        $organization->name = $data['name'];
        if (isset($data['director']))
            $organization->director = $data['director'];
        if (isset($data['phone_director']))
            $organization->phone_director = $data['phone_director'];
        if (isset($data['phone']))
            $organization->phone = $data['phone'];
        if (isset($data['email']))
            $organization->email = $data['email'];
        if (isset($data['website']))
            $organization->website = $data['website'];
        if (isset($data['text']))
            $organization->text = $data['text'];
        if (isset($data['title']))
            $organization->title = $data['title'];    
        if (isset($data['video_url']))
            $organization->title = $data['video_url'];  
        if (isset($data['logo'])) {
            $this->_uploadImageControl($data['logo'], $organization, 'logo', 'app/public/organizations/logos/');
        }
        $organization->save();

        if (!empty($data['images'])) {

            foreach ($data['images'] as $image) {
                $organizationImage = new OrganizationImage();
                $image = $organization->uploadImage($image);
                $organizationImage->organization_id = $organization->id;
                $organizationImage->image = $image;
                $organizationImage->save();
            }
        }

        if ($data['managers_count'] > 0) {
            for ($i = 1; $i <= $data['managers_count']; $i++) {
                if (!empty($data['manager_phone_' . $i])) {
                    if (!$manager = OrganizationManager::where('phone', $data['manager_phone_' . $i])->first()) {
                        $manager = new OrganizationManager();
                    }
                    $manager->organization_id = $organization->id;
                    $manager->phone = $data['manager_phone_' . $i];
                    $manager->name = $data['manager_name_' . $i];
                    $manager->save();
                }
            }
        }
        return $organization;
    }

    public function destroy($id)
    {
        $organization = Organization::find($id);
        if(!$organization) {
            abort(404);
        }
        Job::where('organization_id',$id)->update(['organization_id' => null]);
        $organization->managers()->delete();
        $organization->delete();
        return redirect()->back()->with('message','Organization successfully removed');
    }
}
