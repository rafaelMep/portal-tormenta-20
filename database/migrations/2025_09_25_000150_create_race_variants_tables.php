<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Variantes (subtipos) de uma raça (ex.: Moreau: Coruja/Hiena/… ; Golem: Chassi/Fonte/Tamanho…)
        Schema::create('race_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('race_id')->constrained('races')->cascadeOnDelete();
            $table->string('key', 64);          // ex.: 'coruja', 'hiena', 'raposa'
            $table->string('name', 120);        // ex.: 'Herança da Coruja'
            $table->text('summary')->nullable();
            $table->json('meta')->nullable();   // recursos específicos (armas naturais, sentidos, desloc., etc.)
            $table->timestamps();

            $table->unique(['race_id', 'key']);
        });

        // Ajustes de atributos da variante (igual à tabela race_attribute_mods, mas por variante)
        Schema::create('race_variant_attribute_mods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('race_variant_id')->constrained('race_variants')->cascadeOnDelete();
            $table->string('mode', 10)->default('fixed'); // fixed | choice
            $table->string('attribute', 3)->nullable();   // FOR DES CON INT SAB CAR (null quando mode=choice)
            $table->smallInteger('modifier');
            $table->unsignedTinyInteger('quantity')->default(1); // p/ choice
            $table->json('exclusions')->nullable(); // p/ choice (ex.: ['CAR'])
            $table->string('notes', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('race_variant_attribute_mods');
        Schema::dropIfExists('race_variants');
    }
};
