<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PassportResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $image = asset('storage/passport_images').'/';

        return [
        'id'=>$this->id,
        'user_id'=>$this->user_id,
        'id_number'=>$this->id_number,
        'image'=>$image.$this->image,
        ];
        }
}
