<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Brokenlink extends Model
{
    //
    protected $dateFormat = 'Y/m/d H:m:i';
    public function link()
    {
        return $this->belongsTo('App\Content');
    }
}
