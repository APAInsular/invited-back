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
            $table->string('Name', 400);
            $table->string('First_Surname', 400);
            $table->string('Second_Surname', 400);
            $table->text('Extra_Information')->nullable();
            $table->text('Allergy')->nullable();
            $table->string('Feeding', 400);
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
