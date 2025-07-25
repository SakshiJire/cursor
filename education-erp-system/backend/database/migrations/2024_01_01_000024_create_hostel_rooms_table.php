<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hostel_rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hostel_id')->constrained()->onDelete('cascade');
            $table->string('room_number');
            $table->integer('floor');
            $table->enum('room_type', ['single', 'double', 'triple', 'dormitory']);
            $table->integer('bed_capacity');
            $table->integer('occupied_beds')->default(0);
            $table->decimal('monthly_rent', 8, 2);
            $table->json('facilities')->nullable(); // AC, attached bathroom, etc.
            $table->enum('status', ['available', 'occupied', 'maintenance', 'unavailable'])->default('available');
            $table->timestamps();

            $table->unique(['hostel_id', 'room_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hostel_rooms');
    }
};