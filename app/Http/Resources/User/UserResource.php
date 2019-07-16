<?php

namespace App\Http\Resources\User;

use App\Http\Resources\Media\MediaCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'=>$this->id,
            'name'=>$this->name,
            'email'=>$this->email,
            'registered'=>$this->created_at,
            'media'=>!empty($this->getMedia('user_images')->all())?new MediaCollection($this->getMedia('user_images')):null,
        ];
    }
}
