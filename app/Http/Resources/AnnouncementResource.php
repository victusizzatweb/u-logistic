<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AnnouncementResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'=>$this->id,
            'name'=>$this->name,
            'weight'=>$this->weight,
            'pick_up_address'=>$this->pick_up_address,
            'shipping_address'=>$this->shipping_address,
            'time'=>$this->time,
            'description'=>$this->description,
            'user_id'=>$this->user_id,
            'role_id'=>$this->role_id,
            'status'=>$this->status,
            'get_latitude'=>$this->get_latitude, 
            'get_longitude'=>$this->get_longitude,
            'to_go_latitude'=>$this->to_go_latitude,
            'to_go_longitude'=>$this->to_go_longitude,
            'driver_id'=>$this->driver_id,
            'price'=>$this->price,
            'image'=>AImageResource::collection($this->images),
            'user' => new UserResource($this->whenLoaded('user')),
        ];
    }
}
