<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Grupos de escolhas (ex.: chassi, energy, size, marvel)
        Schema::create('race_choice_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('race_id')->constrained('races')->cascadeOnDelete();
            $table->string('key', 32);              // 'chassis' | 'energy' | 'size' | 'marvel' | ...
            $table->string('name', 64);
            $table->unsignedTinyInteger('min_choices')->default(1);
            $table->unsignedTinyInteger('max_choices')->default(1);
            $table->boolean('required')->default(true);
            $table->unsignedSmallInteger('sort')->default(0);
            $table->json('meta')->nullable();       // regras extras (ex.: "pick_per_tier", etc.)
            $table->timestamps();

            $table->unique(['race_id', 'key']);
        });

        // Opções dentro de um grupo (ex.: 'barro', 'ferro', 'vapor'...)
        Schema::create('race_choice_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained('race_choice_groups')->cascadeOnDelete();
            $table->string('key', 32);           // 'barro' | 'ferro' | 'elemental-fogo' | ...
            $table->string('name', 64);
            $table->text('summary')->nullable(); // descrição amigável
            $table->json('meta')->nullable();    // efeitos estruturados (size override, speed, flags...)
            $table->unsignedSmallInteger('sort')->default(0);
            $table->boolean('is_official')->default(true);
            $table->foreignId('created_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['group_id', 'key']);
        });

        // Textos de regras/efeitos (enquanto não sistematizamos 100% em meta)
        Schema::create('race_effect_texts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('race_id')->constrained('races')->cascadeOnDelete();
            $table->foreignId('choice_option_id')->nullable()->constrained('race_choice_options')->cascadeOnDelete();
            $table->text('text');                // texto da habilidade/regra
            $table->json('tags')->nullable();    // ['imunidade','natural-weapon','velocidade',...]
            $table->unsignedSmallInteger('sort')->default(0);
            $table->timestamps();
        });

        // Vincular modificadores de atributo a uma opção (quando aplicável)
        Schema::table('race_attribute_mods', function (Blueprint $table) {
            $table->foreignId('choice_option_id')
                ->nullable()
                ->after('race_id')
                ->constrained('race_choice_options')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('race_attribute_mods', function (Blueprint $table) {
            $table->dropConstrainedForeignId('choice_option_id');
        });

        Schema::dropIfExists('race_effect_texts');
        Schema::dropIfExists('race_choice_options');
        Schema::dropIfExists('race_choice_groups');
    }
};
