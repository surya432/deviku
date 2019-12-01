<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTrigerUpdateBackup extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        // DB::unprepared('CREATE TRIGGER tupdate_backupsFILES AFTER UPDATE ON `backups` FOR EACH ROW
        // BEGIN
        // INSERT INTO trashes SET  idcopy=old.f720p, token= (Select tokenDriveAdmin from settings where id="1");
        // END');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        // DB::unprepared('DROP TRIGGER `tupdate_backupsFILES');

    }
}
