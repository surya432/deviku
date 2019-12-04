<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTrigerBackup extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        // DB::unprepared('DROP TRIGGER `update_backups');
        // DB::unprepared('CREATE TRIGGER update_backups AFTER UPDATE ON `contents` FOR EACH ROW
        // BEGIN
        // DELETE FROM `backups` WHERE backups.url = old.url;
        // DELETE FROM `backups` WHERE backups.url = old.url;
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
        // DB::unprepared('DROP TRIGGER `update_backups');
    }
}
