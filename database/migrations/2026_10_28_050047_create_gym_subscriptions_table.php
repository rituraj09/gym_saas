<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gym_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gym_id')->constrained('gyms')->cascadeOnDelete();
            $table->foreignId('saas_plan_id')->constrained('saas_plans')->restrictOnDelete();
            $table->foreignId('saas_subscription_status_id')->constrained('saas_subscription_statuses')->restrictOnDelete();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->dateTime('trial_ends_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gym_subscriptions');
    }
};
