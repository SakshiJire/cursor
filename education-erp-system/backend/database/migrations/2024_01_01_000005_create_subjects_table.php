<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institute_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('code')->nullable();
            $table->text('description')->nullable();
            $table->enum('type', ['theory', 'practical', 'both'])->default('theory');
            $table->integer('max_marks')->default(100);
            $table->integer('pass_marks')->default(35);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();

            $table->unique(['institute_id', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subjects');
    }
};