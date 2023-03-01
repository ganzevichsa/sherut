<?php
namespace App\Traits;

use App\Http\Resources\SubcategoryResource;
use Illuminate\Support\Facades\Lang;

trait GlobalLines
{
    protected $uniqueFieldsForSimpleTable = [
        'name'
    ];
    protected $resourcesPath = 'App\Http\Resources\\';
    public function _workingWithSimpleTable($model, $data, $id = null)
    {
        if(!$id) {
            $model = new $model;
            $rowExists = $model;
        }
        foreach($data as $key => $value) {
            $rowExists = $rowExists->where($key,$value);
            $model->$key = $value;
        }
        if($rowExists->count()) {
            return $rowExists->first();
        }
        $model->save();
        return $model;
    }

    public function _getDataFromTable($model, $conditions, $id = null) {
        if(!$id) {
            $model = new $model;
        }
        $resource = $this->resourcesPath.class_basename($model).'Resource';
        foreach($conditions as $key => $value) {
            $model = $model->where($key, $value);
        }
        if($id || $model->get()->count() == 1) {
            return new $resource($model->first());
        }
        return $resource::collection($model->get());
    }

    public function _getCategoryFromTranslations($text)
    {
        foreach (Lang::get('main.categories') as $key => $translation) {
            if(strpos($text, $translation) !== false) {
                return Lang::get('main.categories.'.$key);
            }
        }
    }

    public function _workingWithParentAndChildTables($parentName, $childrenName, $parentModel, $childrenModel, $parentSlug, $childrenSlug)
    {
        $data = [
            $parentSlug . "_id" => null,
            $childrenSlug . "_id" => null,
        ];
        if (!empty($parentName)) {
            $parent = $this->_workingWithSimpleTable($parentModel, ['name' => $parentName]);
            $data[$parentSlug . '_id'] = $parent->id;
        }
        if (empty($childrenName)) {
            $childrenName = Lang::get('main.general');
        }
        if ($data[$parentSlug . '_id']) {
            $children = $childrenModel::where($parentSlug . '_id', $data[$parentSlug . '_id'])->where('name', $childrenName)->first();
            if ($children) {
                $data[$childrenSlug . '_id'] = $children->id;
            } else {
                $children = $this->_workingWithSimpleTable($childrenModel, ['name' => $childrenName, $parentSlug . '_id' => $data[$parentSlug . '_id']]);
                $data[$childrenSlug . '_id'] = $children ? $children->id : null;
            }
        } else {
            $children = $childrenModel::where('name', $childrenName)->first();
            if (!$children) {
                $children = $this->_workingWithSimpleTable($childrenModel, ['name' => $childrenName]);
            }
            $data[$childrenSlug . '_id'] = $children ? $children->id : null;
        }
        return $data;
    }

    public function _getStringBetween($string, $start, $end)
    {
        $string = ' ' . $string;
        $ini = strpos($string, $start);
        if ($ini == 0) return '';
        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;
        return substr($string, $ini, $len);
    }

    public function _uploadImage($image)
    {
        $image = '';
        try{
            $imageLink = file_get_contents($image);
            $image = time().'.png';
            $new = storage_path('app/public/jobs/').'/'.$image;
            file_put_contents($new, $imageLink);
        }catch(\Exception $exception) {

        }
        return $image;
    }

    private function _uploadImageControl($image, &$model, $field, $path)
    {
        if ($model->$field) {
            if (file_exists(storage_path($path . $model->$field))) {
                unlink(storage_path($path . $model->$field));
            }
        }
        $name = time() . '.' . $image->getClientOriginalExtension();
        $destinationPath = storage_path($path);
        $image->move($destinationPath, $name);
        $model->$field = $name;
    }
}
