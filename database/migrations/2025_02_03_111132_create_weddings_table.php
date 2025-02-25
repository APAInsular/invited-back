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
            // $table->time('Ceremony_Start_Time');
            // $table->time('Lunch_Start_Time');
            // $table->time('Dinner_Start_Time');
            // $table->time('Party_Start_Time');
            // $table->time('Party_Finish_Time');
            $table->unsignedBigInteger('user_id'); // Usuario que se casa
            $table->unsignedBigInteger('location_id');

            // $table->unsignedBigInteger('partner_id'); // Pareja del usuario
            // $table->string(column:'user_name');
            // $table->string(column:'partner_name');
            $table->string('dressCode')->nullable()->default('Ninguno');;
            $table->date('weddingDate');
            $table->string('musicUrl');
            $table->string('musicTitle');
            $table->string(column:'groomDescription');
            $table->string(column:'brideDescription');
            $table->string('customMessage');
            $table->string(column:'foodType');
            $table->integer(column:'guestCount');
            $table->string(column:'template');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            // $table->foreign('partner_id')->references('id')->on('partners')->onDelete('cascade');
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
