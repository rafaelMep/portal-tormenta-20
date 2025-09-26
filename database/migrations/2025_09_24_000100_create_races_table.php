<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('races', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name');
            $table->string('size', 16)->default('Médio');
            $table->unsignedTinyInteger('speed')->nullable();
            $table->string('creature_type', 32)->nullable();
            $table->string('source', 32)->default('T20');
            $table->text('summary')->nullable();
            $table->json('meta')->nullable();
            $table->boolean('is_official')->default(true)->index();
            $table->foreignId('created_by_id')->nullable()
                ->constrained('users')->nullOnDelete();

            $table->timestamps();
        });

        Schema::create('race_attribute_mods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('race_id')->constrained('races')->cascadeOnDelete();
            $table->string('mode', 10)->default('fixed');
            $table->string('attribute', 3)->nullable();
            $table->smallInteger('modifier');
            $table->unsignedTinyInteger('quantity')->default(1);
            $table->json('exclusions')->nullable();
            $table->string('notes', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('race_attribute_mods');
        Schema::dropIfExists('races');
    }
};
