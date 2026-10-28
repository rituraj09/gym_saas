<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trainers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->foreignId('gym_id')->constrained('gyms')->cascadeOnDelete();
            $table->string('trainer_code', 30)->unique();
            $table->string('specialization', 100)->nullable();
            $table->string('qualification', 100)->nullable();
            $table->unsignedTinyInteger('experience_years')->default(0);
            $table->date('joining_date');
            $table->string('profile_photo')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trainers');
    }
};
