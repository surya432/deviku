<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class masterlinks extends Model
{
    //
    protected $fillable = ['status','kualitas','url','apikey','content_id','drive'];
    public function Content()
    {
        return $this->belongsTo(Content::class);
    }
}
