<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staff', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('employee_id')->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->date('date_of_birth');
            $table->enum('gender', ['male', 'female', 'other']);
            $table->string('phone');
            $table->string('emergency_contact');
            $table->text('address');
            $table->string('city');
            $table->string('state');
            $table->string('pincode');
            $table->enum('designation', ['principal', 'teacher', 'assistant_teacher', 'admin', 'helper', 'security']);
            $table->string('qualification');
            $table->text('experience')->nullable();
            $table->date('joining_date');
            $table->decimal('salary', 10, 2)->nullable();
            $table->string('photo')->nullable();
            $table->json('documents')->nullable(); // Store document file paths
            $table->enum('status', ['active', 'inactive', 'terminated'])->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff');
    }
};