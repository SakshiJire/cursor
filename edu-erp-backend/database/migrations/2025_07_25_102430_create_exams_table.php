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
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institution_id')->constrained()->onDelete('cascade');
            $table->foreignId('academic_year_id')->constrained()->onDelete('cascade');
            $table->string('name'); // Unit Test 1, Mid Term, Final Exam, etc.
            $table->text('description')->nullable();
            $table->enum('type', ['unit_test', 'mid_term', 'final', 'practical', 'viva']);
            $table->date('start_date');
            $table->date('end_date');
            $table->json('classes')->nullable(); // Classes participating
            $table->json('subjects')->nullable(); // Subjects included
            $table->decimal('total_marks', 8, 2)->default(100);
            $table->decimal('pass_marks', 8, 2)->default(40);
            $table->boolean('result_published')->default(false);
            $table->enum('status', ['scheduled', 'ongoing', 'completed', 'cancelled'])->default('scheduled');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exams');
    }
};
