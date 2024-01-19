<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AudienceResource extends JsonResource
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
			'number' => $this->number,
			'name' => $this->name,
			'email' => $this->email,
			'phone' => $this->phone,
			'test' => TestResource::make($this->whenLoaded('test')),
			'points' => $this->points,
            'created_at' => $this->created_at,
            'last_update' => $this->updated_at,
        ];
    }
}
