<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MyAutoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $image = asset('storage/myAuto').'/';
        // return $image.$this->image;
        return [
        'id'=>$this->id,
        'user_id'=>$this->user_id,
        'transport_number'=>$this->transport_number,
        'transport_model'=>$this->transport_model,
        'transport_capacity'=>$this->transport_capacity,
        'image'=>$image.$this->image,
    ];
    }
}
