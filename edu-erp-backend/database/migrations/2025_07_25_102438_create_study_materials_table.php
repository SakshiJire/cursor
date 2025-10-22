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
        Schema::create('study_materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institution_id')->constrained()->onDelete('cascade');
            $table->foreignId('class_id')->constrained()->onDelete('cascade');
            $table->foreignId('subject_id')->constrained()->onDelete('cascade');
            $table->foreignId('uploaded_by')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('type', ['video', 'pdf', 'image', 'audio', 'assignment', 'link']);
            $table->string('file_path')->nullable();
            $table->string('file_size')->nullable();
            $table->string('external_link')->nullable();
            $table->date('assignment_due_date')->nullable();
            $table->integer('max_marks')->nullable();
            $table->json('access_restrictions')->nullable(); // Specific students or all
            $table->integer('download_count')->default(0);
            $table->boolean('is_downloadable')->default(true);
            $table->enum('status', ['active', 'inactive', 'archived'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('study_materials');
    }
};
