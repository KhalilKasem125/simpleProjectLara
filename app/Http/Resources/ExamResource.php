<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExamResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'subject_id' => $this->subject_id,
            'questions_number' => $this->questions_number,
            'exam_time' => $this->exam_time,
            'success_degree' => $this->success_degree,
            'questions' => QuestionResource::collection($this->whenLoaded('questions'))
        ];
    }
}
