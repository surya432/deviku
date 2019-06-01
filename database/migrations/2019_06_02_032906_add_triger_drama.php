<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTrigerDrama extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        DB::unprepared('CREATE TRIGGER delete_drama AFTER DELETE ON `dramas` FOR EACH ROW
        BEGIN
           DELETE FROM `brokenlinks` WHERE brokenlinks.contents_id in (select id from contents where drama_id = old.id);
           DELETE FROM `contents` WHERE contents.drama_id = old.id;
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
        DB::unprepared('DROP TRIGGER `delete_drama');

    }
}
