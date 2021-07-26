<?php

namespace App\Http\Resources;

use App\FaqAnswer;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class FaqResource extends JsonResource
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
            'question' => $this->question,
            'answers_count' => $this->answers()->count(),
            'created_at' => new Carbon($this->created_at),
            'job' => new SmallJobResource($this->job),
            'user' => new UsersResource($this->user),
            'hr' => new UsersResource($this->hr),
            'answers' => FaqAnswerResource::collection($this->answers()->where('status',FaqAnswer::ACCEPTED)->get())
        ];
    }
}
