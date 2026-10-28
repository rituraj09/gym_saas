<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->foreignId('gym_id')->constrained('gyms')->cascadeOnDelete();
            $table->string('member_code', 30)->unique();
            $table->date('date_of_birth')->nullable();
            $table->foreignId('gender_id')->nullable()->constrained('gender_types')->restrictOnDelete();
            $table->foreignId('blood_group_id')->nullable()->constrained('blood_group_types')->restrictOnDelete();
            $table->text('address')->nullable();
            $table->string('emergency_contact_name', 100)->nullable();
            $table->string('emergency_contact_phone', 20)->nullable();
            $table->date('joining_date');
            $table->string('profile_photo')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('gym_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};
