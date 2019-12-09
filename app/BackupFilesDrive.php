<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BackupFilesDrive extends Model
{
    //
    protected $fillable = ['url', 'title','f720p','tokenfcm '];
    protected $table = 'backups';
    // protected $dateFormat = 'Y/m/d H:m:i';
    public function contents()
    {
        return $this->belongsTo(\App\BackupFilesDrive::class);
    }
}
