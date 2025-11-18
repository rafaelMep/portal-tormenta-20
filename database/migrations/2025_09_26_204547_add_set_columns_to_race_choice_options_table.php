<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Adiciona colunas usadas pelos "sets" sem quebrar o que já existe para "groups"
        Schema::table('race_choice_options', function (Blueprint $table) {
            if (!Schema::hasColumn('race_choice_options', 'set_id')) {
                // FK opcional para race_choice_sets (linhas de group continuarão com set_id = null)
                $table->foreignId('set_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('race_choice_sets')
                    ->cascadeOnDelete();
            }
            if (!Schema::hasColumn('race_choice_options', 'value')) {
                $table->string('value', 32)->nullable()->after('set_id');
            }
            if (!Schema::hasColumn('race_choice_options', 'label')) {
                $table->string('label', 120)->nullable()->after('value');
            }
        });
    }

    public function down(): void
    {
        Schema::table('race_choice_options', function (Blueprint $table) {
            if (Schema::hasColumn('race_choice_options', 'set_id')) {
                $table->dropConstrainedForeignId('set_id'); // remove FK + coluna
            }
            if (Schema::hasColumn('race_choice_options', 'value')) {
                $table->dropColumn('value');
            }
            if (Schema::hasColumn('race_choice_options', 'label')) {
                $table->dropColumn('label');
            }
        });
    }
};
