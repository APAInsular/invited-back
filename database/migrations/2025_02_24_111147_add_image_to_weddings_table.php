<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('weddings', function (Blueprint $table) {
            $table->string('image')->nullable(); // Añadir la columna image para guardar la ruta de la imagen
        });
    }

    public function down()
    {
        Schema::table('weddings', function (Blueprint $table) {
            $table->dropColumn('image'); // Eliminar la columna image
        });
    }
};
