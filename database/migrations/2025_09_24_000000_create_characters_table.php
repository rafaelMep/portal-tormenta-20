<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('characters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->string('name');
            $table->unsignedTinyInteger('level')->default(1);
            $table->string('race')->nullable();
            $table->string('origin')->nullable();
            $table->string('class')->nullable();
            $table->string('deity')->nullable();

            $table->smallInteger('str')->default(0);
            $table->smallInteger('dex')->default(0);
            $table->smallInteger('con')->default(0);
            $table->smallInteger('int')->default(0);
            $table->smallInteger('wis')->default(0);
            $table->smallInteger('cha')->default(0);

            $table->integer('hp_max')->default(0);
            $table->integer('hp_current')->default(0);
            $table->integer('mp_max')->default(0);
            $table->integer('mp_current')->default(0);

            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('characters');
    }
};
