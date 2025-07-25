<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exam_types', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Unit Test, Mid-term, Final, etc.
            $table->text('description')->nullable();
            $table->integer('weightage')->default(100); // Percentage weightage
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('exam_type_id')->constrained()->onDelete('cascade');
            $table->foreignId('academic_year_id')->constrained()->onDelete('cascade');
            $table->foreignId('class_id')->constrained()->onDelete('cascade');
            $table->foreignId('subject_id')->constrained()->onDelete('cascade');
            $table->date('exam_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('max_marks')->default(100);
            $table->text('instructions')->nullable();
            $table->enum('status', ['scheduled', 'ongoing', 'completed', 'cancelled'])->default('scheduled');
            $table->timestamps();
        });

        Schema::create('exam_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->integer('marks_obtained')->nullable();
            $table->string('grade')->nullable();
            $table->text('remarks')->nullable();
            $table->boolean('is_absent')->default(false);
            $table->timestamps();
            
            $table->unique(['exam_id', 'student_id']);
        });

        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignId('class_id')->constrained()->onDelete('cascade');
            $table->foreignId('academic_year_id')->constrained()->onDelete('cascade');
            $table->date('activity_date');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->string('venue')->nullable();
            $table->foreignId('staff_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('type', ['sports', 'cultural', 'educational', 'celebration', 'trip', 'other'])->default('other');
            $table->text('requirements')->nullable();
            $table->enum('status', ['planned', 'ongoing', 'completed', 'cancelled'])->default('planned');
            $table->timestamps();
        });

        Schema::create('activity_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activity_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->enum('participation_status', ['registered', 'participated', 'absent'])->default('registered');
            $table->text('remarks')->nullable();
            $table->timestamps();
            
            $table->unique(['activity_id', 'student_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_participants');
        Schema::dropIfExists('activities');
        Schema::dropIfExists('exam_results');
        Schema::dropIfExists('exams');
        Schema::dropIfExists('exam_types');
    }
};