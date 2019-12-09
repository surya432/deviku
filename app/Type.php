<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Drama;

class Type extends Model
{
    //
    public function Drama()
    {
        return $this->hasMany(Drama::class);
    }
}
