<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Drama;

class Content extends Model
{
    //
    protected $dateFormat = 'Y/m/d H:m:i';

    public function drama()
    {
        return $this->belongsTo(Drama::class);
    }
}
