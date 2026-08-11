<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('spread_positions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('spread_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('orden');        // 1, 2, 3...
            $table->string('nombre_posicion');            // "Situación actual"
            $table->text('descripcion')->nullable();      // qué significa esa posición
            $table->float('coord_x');                      // posición visual en el layout (%)
            $table->float('coord_y');
            $table->unsignedSmallInteger('rotacion')->default(0); // grados, para cartas cruzadas tipo Cruz Celta
            $table->timestamps();

            $table->unique(['spread_id', 'orden']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spread_positions');
    }
};