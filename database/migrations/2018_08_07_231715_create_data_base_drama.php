<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\drama;
class CreateDataBaseDrama extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dramas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title');
            $table->string('country_id');
            $table->string('type_id');
            //$table->string('type_id');
            $table->string('status');
            $table->string('slug');
            $table->string('folderid');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dramas');
    }
}
