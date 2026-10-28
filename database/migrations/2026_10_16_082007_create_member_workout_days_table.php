<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('member_workout_days', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_exercise_plan_id')->constrained('member_exercise_plans')->cascadeOnDelete();
            $table->unsignedTinyInteger('day_of_week'); // 1=Mon, 7=Sun
            $table->foreignId('exercise_template_id')->constrained('exercise_templates')->restrictOnDelete();
            $table->string('notes')->nullable();
            $table->unsignedTinyInteger('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('member_workout_days');
    }
};
