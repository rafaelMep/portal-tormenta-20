<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('skills', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name');
            $table->enum('attr_key', ['FOR', 'DES', 'CON', 'INT', 'SAB', 'CAR']);
            $table->boolean('trained_only')->default(false);
            $table->boolean('armor_penalty')->default(false);
            $table->enum('type', ['skill', 'save', 'attack'])->default('skill');
            $table->boolean('has_specialization')->default(false);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('character_skills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('character_id')->constrained('characters')->cascadeOnDelete();
            $table->foreignId('skill_id')->constrained('skills')->cascadeOnDelete();

            $table->boolean('is_trained')->default(false);
            $table->smallInteger('misc_mod')->default(0);
            $table->string('specialization')->nullable();

            $table->timestamps();

            $table->unique(['character_id', 'skill_id', 'specialization']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('character_skills');
        Schema::dropIfExists('skills');
    }
};
