<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 1) Conjunto de escolhas (ex.: “+1 em 3 atributos”, “+1 exceto CAR”)
        if (!Schema::hasTable('race_choice_sets')) {
            Schema::create('race_choice_sets', function (Blueprint $table) {
                $table->id();
                $table->foreignId('race_id')->constrained('races')->cascadeOnDelete();
                $table->string('key', 64);                 // ex.: attr_plus1_x3, lefou_plus1_except_car
                $table->string('label', 120)->nullable();  // rótulo amigável
                $table->unsignedTinyInteger('min_picks')->default(1);
                $table->unsignedTinyInteger('max_picks')->default(1);
                $table->json('constraints')->nullable();   // ex.: { "exclude": ["CAR"] }
                $table->timestamps();
                $table->unique(['race_id', 'key']);
            });
        }

        // 2) Opções dentro do set (ex.: FOR, DES, CON…)
        if (!Schema::hasTable('race_choice_options')) {
            Schema::create('race_choice_options', function (Blueprint $table) {
                $table->id();
                $table->foreignId('set_id')->constrained('race_choice_sets')->cascadeOnDelete();
                $table->string('value', 32);              // ex.: FOR, DES
                $table->string('label', 120)->nullable(); // rótulo amigável
                $table->json('meta')->nullable();         // livre
                $table->timestamps();
                $table->unique(['set_id', 'value']);
            });
        } else {
            // Se a tabela já existe mas não tem a FK para sets, adiciona.
            if (!Schema::hasColumn('race_choice_options', 'set_id')) {
                Schema::table('race_choice_options', function (Blueprint $table) {
                    $table->foreignId('set_id')
                        ->after('id')
                        ->nullable()
                        ->constrained('race_choice_sets')
                        ->cascadeOnDelete();
                });
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('race_choice_options');
        Schema::dropIfExists('race_choice_sets');
    }
};
