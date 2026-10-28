<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trainer_member_assignments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('gym_id')
                ->constrained('gyms')
                ->cascadeOnDelete();

            $table->foreignId('trainer_id')
                ->constrained('trainers')
                ->cascadeOnDelete();

            $table->foreignId('member_id')
                ->constrained('members')
                ->cascadeOnDelete();

            $table->foreignId('assignment_status_id')
                ->constrained('assignment_statuses')
                ->restrictOnDelete();

            $table->date('start_date');
            $table->date('end_date')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Custom short index names to stay within MySQL's 64-character limit
            $table->index(
                ['gym_id', 'member_id', 'assignment_status_id'],
                'tma_gym_member_status_idx'
            );

            $table->index(
                ['gym_id', 'trainer_id', 'assignment_status_id'],
                'tma_gym_trainer_status_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trainer_member_assignments');
    }
};
