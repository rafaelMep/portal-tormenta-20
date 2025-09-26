<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('race_choice_set_options')) {
            Schema::create('race_choice_set_options', function (Blueprint $table) {
                $table->id();
                $table->foreignId('set_id')->constrained('race_choice_sets')->cascadeOnDelete();
                $table->string('value', 32);              // 'FOR','DES','CON','INT','SAB','CAR'
                $table->string('label', 120)->nullable(); // ex.: “Força”
                $table->json('meta')->nullable();
                $table->timestamps();

                $table->unique(['set_id', 'value']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('race_choice_set_options');
    }
};
