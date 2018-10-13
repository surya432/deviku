<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Country;
use App\Type;
use App\Content;
class Drama extends Model
{
    //
    public function country(){
        return $this->belongsTo(Country::class);
    }
    public function type(){
        return $this->belongsTo(Type::class);
    }
    public function eps(){
        return $this->hasMany(Content::class);
    }
}
