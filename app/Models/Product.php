<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $guarded=[];
    protected $with=['reviews'];
    public function reviews(){
        return $this->hasMany(Review::class);
    }
}
