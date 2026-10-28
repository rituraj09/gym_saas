<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exercise_template_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exercise_template_id')->constrained('exercise_templates')->cascadeOnDelete();
            $table->foreignId('exercise_id')->constrained('exercises')->restrictOnDelete();
            $table->unsignedTinyInteger('sets');
            $table->unsignedTinyInteger('reps');
            $table->unsignedSmallInteger('rest_seconds')->default(60);
            $table->decimal('weight_kg', 5, 1)->nullable();
            $table->string('notes')->nullable();
            $table->unsignedTinyInteger('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exercise_template_items');
    }
};
