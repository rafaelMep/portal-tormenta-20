<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('race_choice_options') && Schema::hasColumn('race_choice_options', 'set_id')) {
            Schema::table('race_choice_options', function (Blueprint $table) {
                $table->dropConstrainedForeignId('set_id'); // drop FK+col
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('race_choice_options') && !Schema::hasColumn('race_choice_options', 'set_id')) {
            Schema::table('race_choice_options', function (Blueprint $table) {
                $table->foreignId('set_id')->nullable()->constrained('race_choice_sets')->cascadeOnDelete();
            });
        }
    }
};
