<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Drama;
class Content extends Model
{
    //
    public function type(){
        return $this->belongsTo(Drama::class);
    }

}
