<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class masterlinks extends Model
{
    //
    public function Content()
    {
        return $this->belongsTo(Content::class);
    }
}
