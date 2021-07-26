<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserQuizResource extends JsonResource
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
            'quiz' => new QuizzesResource($this->quiz),
            'quiz_answer' => new QuizzeAnswersResource($this->quiz_answer),
            'value' => $this->value,
            'created_at' => (new \DateTime($this->created_at))->format('Y-m-d\TH:i'),
            'updated_at' => (new \DateTime($this->updated_at))->format('Y-m-d\TH:i'),
        ];
    }
}
