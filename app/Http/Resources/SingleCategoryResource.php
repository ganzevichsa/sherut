<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SingleCategoryResource extends JsonResource
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
            $images[] = '/storage/categories/'.$image;
        }
        return [
            'id' => $this->id,
            'name' => $this->name,
            'subcategories' => SimpleTableResource::collection($this->subcategories),
            'images' => $images,
            'video_url' => $this->video_url,
            'value' => $this->value,
            'priority' => $this->priority,
            'min_value' => $this->min_value,
            'text' => $this->text,
            'title' => $this->title,

        ];
    }
}
