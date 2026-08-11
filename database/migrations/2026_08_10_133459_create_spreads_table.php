<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('spreads', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');              // "Cruz Celta"
            $table->string('slug')->unique();       // "cruz-celta"
            $table->text('descripcion')->nullable();
            $table->unsignedTinyInteger('cantidad_cartas');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spreads');
    }
};