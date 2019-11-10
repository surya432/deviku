<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTrigerDramaEps extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        DB::unprepared('CREATE TRIGGER delete_episode_drama AFTER DELETE ON `contents` FOR EACH ROW
            BEGIN
            INSERT INTO trashes SET  idcopy=old.f720p, token= (Select tokenDriveAdmin from settings where id="1");
            INSERT INTO trashes SET  idcopy=old.f360p, token= (Select tokenDriveAdmin from settings where id="1");
                DELETE FROM `backups` WHERE backups.f720p = old.f720p;
                DELETE FROM `brokenlinks` WHERE brokenlinks.contents_id = old.id ;
            END');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        DB::unprepared('DROP TRIGGER `delete_episode_drama');
    }
}
