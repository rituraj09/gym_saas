<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gym_member_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gym_id')->constrained('gyms')->cascadeOnDelete();
            $table->foreignId('member_id')->constrained('members')->cascadeOnDelete();
            $table->foreignId('membership_id')->nullable()->constrained('memberships')->nullOnDelete();
            $table->foreignId('payment_method_id')->constrained('payment_methods')->restrictOnDelete();
            $table->foreignId('payment_status_id')->constrained('payment_statuses')->restrictOnDelete();
            $table->decimal('amount', 10, 2);
            $table->string('transaction_reference')->nullable();
            $table->date('payment_date');
            $table->text('notes')->nullable();
            $table->string('receipt')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['gym_id', 'member_id']);
            $table->index('payment_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gym_member_payments');
    }
};
