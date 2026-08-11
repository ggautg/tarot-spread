<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cards', function (Blueprint $table) {
            $table->id();
            $table->string('name');                 // "The Fool"
            $table->string('name_short')->unique();  // "ar00" -> clave natural de la API
            $table->string('value');                 // "Ar00"
            $table->integer('value_int');            // 0
            $table->enum('type', ['major', 'minor']);
            $table->string('suit')->nullable();      // wands, cups, swords, pentacles (null en majors)
            $table->text('meaning_up');
            $table->text('meaning_rev');
            $table->text('desc');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cards');
    }
};