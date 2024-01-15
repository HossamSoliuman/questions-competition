<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class QuestionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id'=> $this->id,
			'name' => $this->name,
			'a' => $this->a,
			'b' => $this->b,
			'c' => $this->c,
			'd' => $this->d,
			'correct_answer' => $this->correct_answer,
			'category' => CategoryResource::make($this->whenLoaded('category')),
            'created_at' => $this->created_at,
            'last_update' => $this->updated_at,
        ];
    }
}
