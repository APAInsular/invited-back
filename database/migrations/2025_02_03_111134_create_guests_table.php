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
        Schema::create('guests', function (Blueprint $table) {
            $table->id();
            $table->string('name', 400);
            $table->string('firstSurname', 400);
            $table->string('secondSurname', 400)->nullable();
            $table->text('extraInformation')->nullable();
            $table->text('allergy')->nullable();
            $table->string('feeding', 400)->nullable();
            $table->unsignedBigInteger('wedding_id'); // Clave foránea
            $table->timestamps();

            // Definir clave foránea correctamente
            $table->foreign('wedding_id')->references('id')->on('weddings')->onDelete('cascade');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guests');
    }
};
