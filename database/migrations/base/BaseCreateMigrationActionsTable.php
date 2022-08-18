<?php

use DragonCode\LaravelActions\Concerns\Database;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

abstract class BaseCreateMigrationActionsTable extends Migration
{
    use Database;

    public function up()
    {
        if (Schema::hasTable($this->table())) {
            return;
        }

        Schema::create($this->table(), function (Blueprint $table) {
            $table->id();

            $table->string('migration');

            $table->integer('batch');
        });
    }

    public function down()
    {
        Schema::dropIfExists($this->table());
    }
}

;
