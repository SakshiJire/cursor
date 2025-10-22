<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('institution_id')->constrained()->onDelete('cascade');
            $table->foreignId('academic_year_id')->constrained()->onDelete('cascade');
            $table->foreignId('class_id')->constrained()->onDelete('cascade');
            $table->string('admission_number')->unique();
            $table->date('admission_date');
            $table->string('roll_number')->nullable();
            $table->foreignId('parent_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('father_name');
            $table->string('mother_name');
            $table->string('guardian_name')->nullable();
            $table->string('guardian_relation')->nullable();
            $table->string('blood_group')->nullable();
            $table->text('medical_conditions')->nullable();
            $table->string('previous_school')->nullable();
            $table->string('transport_required')->default('no');
            $table->string('hostel_required')->default('no');
            $table->json('documents')->nullable(); // Store uploaded document paths
            $table->text('remarks')->nullable();
            $table->enum('status', ['active', 'inactive', 'transferred', 'graduated'])->default('active');
            $table->timestamps();

            $table->unique(['institution_id', 'admission_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
