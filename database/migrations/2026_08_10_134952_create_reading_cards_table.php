<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reading_cards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reading_id')->constrained()->cascadeOnDelete();
            $table->foreignId('spread_position_id')->constrained();
            $table->foreignId('card_id')->constrained();
            $table->boolean('invertida')->default(false); // upright vs reversed
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reading_cards');
    }
};