<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('readings', function (Blueprint $table) {
            $table->string('server_seed')->nullable()->after('seed');       // secreto, null hasta el reveal
            $table->string('server_seed_hash')->after('server_seed');       // público desde el commit
            $table->string('client_seed')->after('server_seed_hash');       // público desde el commit
            $table->timestamp('revealed_at')->nullable()->after('client_seed');
        });
    }

    public function down(): void
    {
        Schema::table('readings', function (Blueprint $table) {
            $table->dropColumn(['server_seed', 'server_seed_hash', 'client_seed', 'revealed_at']);
        });
    }
};