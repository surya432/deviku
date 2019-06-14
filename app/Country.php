<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Drama;
class Country extends Model
{
    //
    protected $dateFormat = 'Y/m/d H:m:i';

    //protected $table = 'countrys';
    public function drama(){
        return $this->hasOne{Drama::class};
    }
}
