<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BackupFilesDrive extends Model
{
    //
    protected $fillable = ['url', 'title','f720p'];
    protected $table = 'backups';

}
