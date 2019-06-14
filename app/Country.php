<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Drama;
class Country extends Model
{
    //

    //protected $table = 'countrys';
    public function drama(){
        return $this->hasOne{Drama::class};
    }
}
