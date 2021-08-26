<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEveryTimeTable extends Migration
{
    public function up()
    {
        Schema::create('every_time', function (Blueprint $table) {
            $table->uuid('value');
        });
    }

    public function down()
    {
        Schema::dropIfExists('every_time');
    }
}
