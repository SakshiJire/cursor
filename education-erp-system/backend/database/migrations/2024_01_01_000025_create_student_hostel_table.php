<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_hostel', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('hostel_room_id')->constrained()->onDelete('cascade');
            $table->string('bed_number');
            $table->date('allotment_date');
            $table->date('checkout_date')->nullable();
            $table->decimal('monthly_fee', 8, 2);
            $table->decimal('security_deposit', 8, 2)->default(0);
            $table->text('guardian_consent')->nullable();
            $table->string('emergency_contact');
            $table->enum('status', ['active', 'checkout', 'suspended'])->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_hostel');
    }
};