<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('member_diet_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_diet_plan_id')->constrained('member_diet_plans')->cascadeOnDelete();
            $table->date('log_date');
            $table->string('status'); // followed, partially, not_followed
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('member_diet_logs');
    }
};
