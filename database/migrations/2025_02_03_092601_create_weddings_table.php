<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('weddings', function (Blueprint $table) {
            $table->id();
            $table->time('Ceremony_Start_Time');
            $table->time('Lunch_Start_Time');
            $table->time('Dinner_Start_Time');
            $table->time('Party_Start_Time');
            $table->time('Party_Finish_Time');
            $table->enum('Dress_Code', ["etiqueta",""]);
            $table->date('Wedding_Date');
            $table->string('Music');
            $table->timestamps();
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
