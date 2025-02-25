<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('weddings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // Usuario que se casa
            $table->unsignedBigInteger('location_id');

            $table->string('dressCode')->nullable()->default('Ninguno');
            $table->date('weddingDate')->default('2025-01-01');
            $table->string('musicUrl')->nullable()->default('https://example.com/default-music.mp3'); // URL de música por defecto
            $table->string('musicTitle')->nullable()->default('Sin título'); // Título de la música por defecto

            $table->string('groomDescription')->nullable()->default('Descripción del novio no disponible');
            $table->string('brideDescription')->nullable()->default('Descripción de la novia no disponible');
            $table->string('customMessage')->nullable()->default('¡Te invitamos a nuestra boda!');

            $table->string('foodType')->nullable()->default('Internacional');
            $table->integer('guestCount')->default(100);
            $table->string('template')->nullable()->default('classic');

            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('location_id')->references('id')->on('locations')->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('weddings');
    }
};
