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
        Schema::create('attendants', function (Blueprint $table) {
            $table->id();
            $table->string('name', 400)->nullable();
            $table->string('firstSurname', 400)->nullable();
            $table->string('secondSurname', 400)->nullable();
            $table->unsignedBigInteger('guest_id')->nullable(); // Clave foránea
            $table->integer('age')->nullable();
            $table->timestamps();

            // Definir clave foránea correctamente
            $table->foreign('guest_id')->references('id')->on('guests')->onDelete('cascade');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendants');
    }
};
