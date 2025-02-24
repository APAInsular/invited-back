<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('wedding_id'); // Definir la columna antes de la clave foránea
            $table->unsignedBigInteger('location_id'); // Definir la columna antes de la clave foránea

            $table->text('description')->nullable();
            $table->time('time');
            $table->timestamps();
            $table->foreign('wedding_id')->references('id')->on('weddings')->onDelete('cascade');
            $table->foreign('location_id')->references('id')->on('locations')->onDelete('cascade');

        });
    }

    public function down()
    {
        Schema::dropIfExists('events');
    }
};
