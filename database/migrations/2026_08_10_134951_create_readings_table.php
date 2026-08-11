<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('readings', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique(); // para compartir la lectura por link, como en El Corazón de las Cartas
            $table->foreignId('spread_id')->constrained();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); // null = lectura anónima
            $table->string('session_id')->nullable(); // para trackear anónimos si no hay user_id
            $table->string('seed'); // hash reproducible de la tirada
            $table->string('pregunta')->nullable(); // pregunta opcional que el usuario tipeó
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('readings');
    }
};