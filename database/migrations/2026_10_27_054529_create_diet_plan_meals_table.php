<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('diet_plan_meals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('diet_plan_template_id')->constrained('diet_plan_templates')->cascadeOnDelete();
            $table->foreignId('meal_type_id')->constrained('meal_types')->restrictOnDelete();
            $table->time('meal_time')->nullable();
            $table->string('notes')->nullable();
            $table->unsignedTinyInteger('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('diet_plan_meals');
    }
};
