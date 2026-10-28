<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('diet_plan_meal_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('diet_plan_meal_id')->constrained('diet_plan_meals')->cascadeOnDelete();
            $table->string('food_name');
            $table->string('quantity');
            $table->unsignedInteger('calories');
            $table->decimal('protein_g', 5, 1)->default(0);
            $table->decimal('carbs_g', 5, 1)->default(0);
            $table->decimal('fat_g', 5, 1)->default(0);
            $table->string('notes')->nullable();
            $table->unsignedTinyInteger('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('diet_plan_meal_items');
    }
};
