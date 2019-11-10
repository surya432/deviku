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
}
