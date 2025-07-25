<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institute_id')->constrained()->onDelete('cascade');
            $table->string('name'); // e.g., "Class 1", "Grade 10", "Sr. KG"
            $table->string('section')->nullable(); // A, B, C
            $table->integer('capacity')->default(30);
            $table->foreignId('class_teacher_id')->nullable()->constrained('users')->onDelete('set null');
            $table->decimal('annual_fee', 10, 2)->default(0);
            $table->text('description')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();

            $table->unique(['institute_id', 'name', 'section']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('classes');
    }
};