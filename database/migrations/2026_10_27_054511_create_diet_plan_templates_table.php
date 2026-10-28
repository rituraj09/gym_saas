<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('diet_plan_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gym_id')->constrained('gyms')->cascadeOnDelete();
            $table->foreignId('created_by_user_id')->constrained('users')->restrictOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('target_goal')->nullable();
            $table->unsignedInteger('total_calories')->nullable();
            $table->decimal('total_protein_g', 6, 1)->nullable();
            $table->decimal('total_carbs_g', 6, 1)->nullable();
            $table->decimal('total_fat_g', 6, 1)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('diet_plan_templates');
    }
};
