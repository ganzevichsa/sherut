<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrganizationsResource extends JsonResource
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return array
     * @throws \Exception
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'phone' => $this->phone,
            'logo' => $this->logo,
            'created_at' => (new \DateTime($this->created_at))->format('Y-m-d\TH:i'),
            'updated_at' => (new \DateTime($this->updated_at))->format('Y-m-d\TH:i'),
        ];
    }
}
