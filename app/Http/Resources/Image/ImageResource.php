<?php

namespace App\Http\Resources\Image;

use App\Http\Resources\User\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ImageResource extends JsonResource
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
            'storage'=>$this->storage,
            'size'=>$this->size,
            'extension'=>$this->size,
            'user'=>new UserResource($this->user),
            'created_at'=>$this->created_at,
        ];
    }
}
