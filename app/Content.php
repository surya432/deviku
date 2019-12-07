<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Drama;

class Content extends Model
{
    //

    public function drama()
    {
        return $this->belongsTo(Drama::class);
    }
    public function backup(){ 
        return $this->hasMany(\App\BackupFilesDrive::class,'url','url'); 
    }
    public function links(){ 
        return $this->hasMany(\App\masterlinks::class); 
    }
}
