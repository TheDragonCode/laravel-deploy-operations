<?php

use DragonCode\LaravelActions\Concerns\Migrations;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    use Migrations;

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
};
