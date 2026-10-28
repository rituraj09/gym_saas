<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trainers', function (Blueprint $table) {
            $table->date('date_of_birth')->nullable()->after('profile_photo');
            $table->foreignId('gender_id')->nullable()->after('date_of_birth')->constrained('gender_types')->nullOnDelete();
            $table->foreignId('blood_group_id')->nullable()->after('gender_id')->constrained('blood_group_types')->nullOnDelete();
            $table->text('address')->nullable()->after('blood_group_id');
        });
    }

    public function down(): void
    {
        Schema::table('trainers', function (Blueprint $table) {
            $table->dropForeign(['gender_id']);
            $table->dropForeign(['blood_group_id']);
            $table->dropColumn(['date_of_birth', 'gender_id', 'blood_group_id', 'address']);
        });
    }
};
