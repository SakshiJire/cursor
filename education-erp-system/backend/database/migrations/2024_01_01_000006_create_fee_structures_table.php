<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fee_structures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institute_id')->constrained()->onDelete('cascade');
            $table->foreignId('class_id')->constrained()->onDelete('cascade');
            $table->string('fee_type'); // tuition, library, lab, transport, etc.
            $table->decimal('amount', 10, 2);
            $table->enum('frequency', ['monthly', 'quarterly', 'half_yearly', 'yearly', 'one_time']);
            $table->date('due_date');
            $table->decimal('late_fee', 10, 2)->default(0);
            $table->integer('grace_period_days')->default(0);
            $table->text('description')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fee_structures');
    }
};