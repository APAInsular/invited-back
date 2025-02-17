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
            $table->string('Name', 400);
            $table->string('First_Surname', 400);
            $table->string('Second_Surname', 400);
            $table->unsignedBigInteger('guest_id'); // Clave foránea
            $table->integer('age');
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
