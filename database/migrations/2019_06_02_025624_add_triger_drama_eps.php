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
        // DB::unprepared('DROP TRIGGER `delete_episode_drama');

        // DB::unprepared('CREATE TRIGGER delete_episode_drama AFTER DELETE ON `contents` FOR EACH ROW
        //     BEGIN
        //         DELETE FROM `backups` WHERE backups.url = old.url;
        //         DELETE FROM `brokenlinks` WHERE brokenlinks.contents_id = old.id ;
        //     END');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        // DB::unprepared('DROP TRIGGER `delete_episode_drama');
    }
}
