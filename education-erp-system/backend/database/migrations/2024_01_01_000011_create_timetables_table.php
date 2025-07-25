<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('timetables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institute_id')->constrained()->onDelete('cascade');
            $table->foreignId('class_id')->constrained()->onDelete('cascade');
            $table->foreignId('subject_id')->constrained()->onDelete('cascade');
            $table->foreignId('teacher_id')->constrained('users')->onDelete('cascade');
            $table->enum('day_of_week', ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday']);
            $table->time('start_time');
            $table->time('end_time');
            $table->string('room_number')->nullable();
            $table->enum('period_type', ['regular', 'break', 'lunch', 'assembly'])->default('regular');
            $table->timestamps();

            $table->unique(['class_id', 'day_of_week', 'start_time']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('timetables');
    }
};