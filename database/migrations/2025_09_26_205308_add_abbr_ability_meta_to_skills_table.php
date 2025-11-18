<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('skills', function (Blueprint $table) {
            if (!Schema::hasColumn('skills', 'abbr')) {
                $table->string('abbr', 24)->nullable()->after('name');
            }
            if (!Schema::hasColumn('skills', 'ability')) {
                // FOR, DES, CON, INT, SAB, CAR
                $table->string('ability', 3)->nullable()->after('abbr');
            }
            if (!Schema::hasColumn('skills', 'meta')) {
                $table->json('meta')->nullable()->after('ability');
            }
        });
    }

    public function down(): void
    {
        Schema::table('skills', function (Blueprint $table) {
            if (Schema::hasColumn('skills', 'meta')) {
                $table->dropColumn('meta');
            }
            if (Schema::hasColumn('skills', 'ability')) {
                $table->dropColumn('ability');
            }
            if (Schema::hasColumn('skills', 'abbr')) {
                $table->dropColumn('abbr');
            }
        });
    }
};
