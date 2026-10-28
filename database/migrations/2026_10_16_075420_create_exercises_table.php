<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exercises', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->foreignId('muscle_group_id')->constrained('muscle_groups')->restrictOnDelete();
            $table->foreignId('equipment_type_id')->nullable()->constrained('equipment_types')->nullOnDelete();
            $table->foreignId('difficulty_level_id')->constrained('difficulty_levels')->restrictOnDelete();
            $table->text('instructions')->nullable();
            $table->string('image')->nullable();
            $table->string('video_url')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exercises');
    }
};
