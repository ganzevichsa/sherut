<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CategoriesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'icon' => 'http://api.sherutbekalut.co.il/storage/categories/icons/'.$this->icon,
            'text' => $this->text,
            'title' => $this->title,

        ];
    }
}
