<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('memberships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gym_id')->constrained('gyms')->cascadeOnDelete();
            $table->foreignId('member_id')->constrained('members')->cascadeOnDelete();
            $table->foreignId('membership_plan_id')->constrained('membership_plans')->restrictOnDelete();
            $table->foreignId('membership_status_id')->constrained('membership_statuses')->restrictOnDelete();
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('price', 10, 2);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['gym_id', 'member_id']);
            $table->index(['gym_id', 'membership_status_id']);
            $table->index('end_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('memberships');
    }
};
