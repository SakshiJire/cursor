<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('admission_number')->unique();
            $table->string('roll_number')->nullable();
            $table->foreignId('class_id')->constrained()->onDelete('cascade');
            $table->foreignId('academic_year_id')->constrained()->onDelete('cascade');
            $table->string('first_name');
            $table->string('last_name');
            $table->date('date_of_birth');
            $table->enum('gender', ['male', 'female', 'other']);
            $table->string('blood_group')->nullable();
            $table->text('address');
            $table->string('city');
            $table->string('state');
            $table->string('pincode');
            $table->string('photo')->nullable();
            $table->json('medical_info')->nullable(); // Allergies, medical conditions
            $table->json('emergency_contacts')->nullable();
            $table->date('admission_date');
            $table->enum('status', ['active', 'inactive', 'transferred', 'graduated'])->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};