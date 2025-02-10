<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->unsignedBigInteger('wedding_id'); // Definir la columna antes de la clave foránea
            $table->text('descripcion')->nullable();
            $table->time('hora');
            $table->string('ubicacion')->nullable();
            $table->timestamps();
            $table->foreign('wedding_id')->references('id')->on('weddings')->onDelete('cascade');

        });
    }

    public function down()
    {
        Schema::dropIfExists('events');
    }
};
