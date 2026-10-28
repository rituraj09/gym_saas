<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_role_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('user_role_id')->constrained('user_roles')->restrictOnDelete();
            $table->timestamps();

            $table->unique(['user_id', 'user_role_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_role_assignments');
    }
};
