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
            $table->foreignId('institute_id')->constrained()->onDelete('cascade');
            $table->foreignId('class_id')->nullable()->constrained()->onDelete('set null');
            $table->string('admission_number')->unique();
            $table->date('admission_date');
            $table->string('roll_number')->nullable();
            $table->string('father_name');
            $table->string('mother_name');
            $table->string('guardian_name')->nullable();
            $table->string('father_phone')->nullable();
            $table->string('mother_phone')->nullable();
            $table->string('guardian_phone')->nullable();
            $table->string('father_occupation')->nullable();
            $table->string('mother_occupation')->nullable();
            $table->string('blood_group')->nullable();
            $table->text('medical_history')->nullable();
            $table->string('previous_school')->nullable();
            $table->decimal('previous_percentage', 5, 2)->nullable();
            $table->json('documents')->nullable(); // Store uploaded document paths
            $table->text('special_notes')->nullable();
            $table->enum('transport_required', ['yes', 'no'])->default('no');
            $table->enum('hostel_required', ['yes', 'no'])->default('no');
            $table->enum('status', ['active', 'inactive', 'passed_out', 'transferred'])->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};