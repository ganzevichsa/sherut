<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class FaqAnswerResource extends JsonResource
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
            'answer' => $this->answer,
            'is_hr' => $this->is_hr,
            'created_at' => new Carbon($this->created_at)
        ];
    }
}
