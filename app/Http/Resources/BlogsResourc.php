<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BlogsResourc extends JsonResource
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
            'title' => $this->title,
            'category' => $this->category->name .", ".$this->subcategory->name,
            'date' => $this->date,
            'description' => $this->description,
            'author' => $this->author,
            'subtitle' => $this->subtitle,
            'organization' => $this->organization,
            'organisation_logo' => $this->organisation_logo,

            'image' => '/storage/blogs/'.$this->image,
        ];
    }
}
