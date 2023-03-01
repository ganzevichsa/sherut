<?php

namespace App\Http\Resources;

use App\FaqAnswer;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class FaqHrJobsQuestionsResource extends JsonResource
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
            'user_id' => $this->user_id,
            'user_name' => $this->user->name,
            "job_id" => $this->job_id,
            "hr_id" => $this->hr_id,
            "question" => $this->question,
            "answer" => FaqAnswerResource::collection($this->answers()->get()),
            "status" => $this->status,
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at
        ];
    }
}
