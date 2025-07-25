<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('learning_materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institute_id')->constrained()->onDelete('cascade');
            $table->foreignId('class_id')->constrained()->onDelete('cascade');
            $table->foreignId('subject_id')->constrained()->onDelete('cascade');
            $table->foreignId('uploaded_by')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('type', ['video', 'document', 'audio', 'image', 'link', 'assignment']);
            $table->string('file_path')->nullable();
            $table->string('file_name')->nullable();
            $table->bigInteger('file_size')->nullable(); // in bytes
            $table->string('external_link')->nullable();
            $table->date('available_from')->nullable();
            $table->date('available_till')->nullable();
            $table->enum('visibility', ['public', 'class_only', 'private'])->default('class_only');
            $table->integer('download_count')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('learning_materials');
    }
};