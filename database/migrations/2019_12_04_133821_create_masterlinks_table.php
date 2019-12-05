<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMasterlinksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('masterlinks');
        Schema::create('masterlinks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('drive')->nullable();
            $table->string('kualitas')->nullable();
            $table->string('url')->nullable();
            $table->string('status')->nullable();
            $table->longText('apikey')->nullable();
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
        Schema::dropIfExists('masterlinks');
    }
}
