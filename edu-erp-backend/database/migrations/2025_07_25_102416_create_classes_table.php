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
        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institution_id')->constrained()->onDelete('cascade');
            $table->foreignId('academic_year_id')->constrained()->onDelete('cascade');
            $table->string('name'); // e.g., "1st Grade", "Sr. KG", "Class 10", "Plus Two Science"
            $table->string('section')->default('A'); // A, B, C, etc.
            $table->enum('level', ['playgroup', 'nursery', 'jr_kg', 'sr_kg', 'primary', 'middle', 'high', 'higher_secondary', 'undergraduate', 'postgraduate']);
            $table->integer('max_students')->default(40);
            $table->integer('current_students')->default(0);
            $table->string('class_teacher_id')->nullable();
            $table->text('description')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();

            $table->unique(['institution_id', 'academic_year_id', 'name', 'section']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('classes');
    }
};
