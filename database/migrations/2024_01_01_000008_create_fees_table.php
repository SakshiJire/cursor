<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fee_types', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Monthly Fee, Admission Fee, Annual Fee, etc.
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('fees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('fee_type_id')->constrained()->onDelete('cascade');
            $table->foreignId('academic_year_id')->constrained()->onDelete('cascade');
            $table->string('month')->nullable(); // For monthly fees
            $table->decimal('amount', 10, 2);
            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->decimal('pending_amount', 10, 2)->default(0);
            $table->date('due_date');
            $table->date('paid_date')->nullable();
            $table->enum('status', ['pending', 'partial', 'paid', 'overdue'])->default('pending');
            $table->string('payment_method')->nullable(); // cash, online, card
            $table->string('transaction_id')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });

        Schema::create('fee_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fee_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->date('payment_date');
            $table->string('payment_method');
            $table->string('transaction_id')->nullable();
            $table->string('receipt_number')->unique();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fee_payments');
        Schema::dropIfExists('fees');
        Schema::dropIfExists('fee_types');
    }
};