<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institute_id')->constrained()->onDelete('cascade');
            $table->string('vehicle_number')->unique();
            $table->enum('vehicle_type', ['bus', 'van', 'car']);
            $table->integer('capacity');
            $table->string('driver_name');
            $table->string('driver_phone');
            $table->string('driver_license');
            $table->string('conductor_name')->nullable();
            $table->string('conductor_phone')->nullable();
            $table->date('insurance_expiry');
            $table->date('fitness_certificate_expiry');
            $table->decimal('monthly_fee', 8, 2);
            $table->enum('status', ['active', 'maintenance', 'inactive'])->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};