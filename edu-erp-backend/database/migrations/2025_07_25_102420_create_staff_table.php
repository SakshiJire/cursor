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
        Schema::create('staff', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('institution_id')->constrained()->onDelete('cascade');
            $table->string('employee_id')->unique();
            $table->enum('staff_type', ['teaching', 'non_teaching', 'admin']);
            $table->string('designation');
            $table->string('department')->nullable();
            $table->date('joining_date');
            $table->decimal('basic_salary', 10, 2)->default(0);
            $table->json('qualifications')->nullable();
            $table->json('experience')->nullable();
            $table->string('bank_account')->nullable();
            $table->string('pan_number')->nullable();
            $table->string('aadhar_number')->nullable();
            $table->json('subjects')->nullable(); // For teaching staff
            $table->json('classes')->nullable(); // Classes assigned to
            $table->decimal('leave_balance', 5, 2)->default(0);
            $table->json('documents')->nullable();
            $table->text('remarks')->nullable();
            $table->enum('status', ['active', 'inactive', 'terminated'])->default('active');
            $table->timestamps();

            $table->unique(['institution_id', 'employee_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff');
    }
};
