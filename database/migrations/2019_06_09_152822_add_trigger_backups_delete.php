<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTriggerBackupsDelete extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        // DB::unprepared('DROP TRIGGER `delete_backup');
        // DB::unprepared('CREATE TRIGGER delete_backup AFTER DELETE ON `backups` FOR EACH ROW
        // BEGIN
        //     INSERT INTO trashes SET  idcopy=old.f720p, token= (Select token from gmails where tipe="backup" limit 0,1);
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
        // DB::unprepared('DROP TRIGGER `delete_backup');
    }
}
