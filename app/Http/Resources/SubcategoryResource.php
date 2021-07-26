<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SubcategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $images = [];
        foreach($this->images()->pluck('image') as $image) {
            $images[] = '/storage/subcategories/'.$image;
        }
        return [
            'id' => $this->id,
            'category_id' => $this->category ? $this->category->id : null,
            'name' => $this->name,
            'images' => $images,
            'video_url' => $this->video_url,
            'text' => $this->text,
        ];
    }
}
