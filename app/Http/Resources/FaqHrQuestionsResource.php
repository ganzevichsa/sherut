<?php

namespace App\Http\Resources;

use App\FaqAnswer;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class FaqHrQuestionsResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user' => $this->user,
            "job_id" => $this->job_id,
            "hr_id" => $this->hr_id,
            "question" => $this->question,
            "answer" => FaqAnswerResource::collection($this->answers()->orderBy('created_at', 'ASC')->take(1)->get()),
            "status" => $this->status,
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at
        ];
    }
}
