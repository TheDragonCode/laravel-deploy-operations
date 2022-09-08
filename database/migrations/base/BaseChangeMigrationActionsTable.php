<?php

use DragonCode\LaravelActions\Concerns\Database;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

abstract class BaseChangeMigrationActionsTable extends Migration
{
    use Database;

    public function up()
    {
        Schema::table($this->table(), function (Blueprint $table) {
            $table->renameColumn('migration', 'action');

            $table->unsignedInteger('batch')->change();
        });
    }

    public function down()
    {
        Schema::table($this->table(), function (Blueprint $table) {
            $table->renameColumn('action', 'migration');

            $table->integer('batch')->change();
        });
    }
}
