<?php
namespace App\Http\Resources\User;

use Illuminate\Http\Resources\Json\Resource;
class UserCollection extends Resource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'registered_at' => $this->created_at,
            'is_profile_activated' => ($this->enabled)?true:false,
            'href' => [
                'link' => route('user',$this->id)
            ]
        ];
    }
}
