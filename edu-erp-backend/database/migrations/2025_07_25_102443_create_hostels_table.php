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
        Schema::create('hostels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institution_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->enum('type', ['boys', 'girls', 'mixed']);
            $table->text('address');
            $table->string('warden_name')->nullable();
            $table->string('warden_phone')->nullable();
            $table->integer('total_rooms');
            $table->integer('occupied_rooms')->default(0);
            $table->decimal('monthly_fee', 8, 2)->default(0);
            $table->json('amenities')->nullable();
            $table->text('rules')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });

        Schema::create('hostel_rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hostel_id')->constrained()->onDelete('cascade');
            $table->string('room_number');
            $table->enum('room_type', ['single', 'double', 'triple', 'dormitory']);
            $table->integer('capacity');
            $table->integer('occupied_beds')->default(0);
            $table->decimal('monthly_fee', 8, 2)->default(0);
            $table->json('facilities')->nullable();
            $table->enum('status', ['available', 'occupied', 'maintenance'])->default('available');
            $table->timestamps();

            $table->unique(['hostel_id', 'room_number']);
        });

        Schema::create('hostel_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('hostel_room_id')->constrained()->onDelete('cascade');
            $table->date('check_in_date');
            $table->date('check_out_date')->nullable();
            $table->text('special_requirements')->nullable();
            $table->enum('status', ['active', 'checked_out', 'transferred'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hostel_assignments');
        Schema::dropIfExists('hostel_rooms');
        Schema::dropIfExists('hostels');
    }
};
