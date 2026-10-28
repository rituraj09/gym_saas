<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('member_workout_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_exercise_plan_id')->constrained('member_exercise_plans')->cascadeOnDelete();
            $table->foreignId('member_workout_day_id')->constrained('member_workout_days')->cascadeOnDelete();
            $table->foreignId('workout_log_status_id')->constrained('workout_log_statuses')->restrictOnDelete();
            $table->date('workout_date');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('member_workout_logs');
    }
};
