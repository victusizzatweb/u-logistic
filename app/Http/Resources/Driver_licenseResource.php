<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class Driver_licenseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $image = asset('storage/driverLicense_images').'/';

        return [
        'id'=>$this->id,
        'user_id'=>$this->user_id,
        'certificate_number'=>$this->certificate_number,
        'categories'=>$this->categories,
        'image'=>$image.$this->image,
        ];
    }
}
