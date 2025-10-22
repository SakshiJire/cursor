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
        Schema::create('transports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institution_id')->constrained()->onDelete('cascade');
            $table->string('vehicle_number');
            $table->string('vehicle_type'); // bus, van, car
            $table->string('route_name');
            $table->json('stops')->nullable(); // Array of stops with timings
            $table->time('start_time');
            $table->time('end_time');
            $table->string('driver_name');
            $table->string('driver_phone');
            $table->string('conductor_name')->nullable();
            $table->string('conductor_phone')->nullable();
            $table->integer('capacity');
            $table->integer('current_students')->default(0);
            $table->decimal('monthly_fee', 8, 2)->default(0);
            $table->text('route_description')->nullable();
            $table->enum('status', ['active', 'inactive', 'maintenance'])->default('active');
            $table->timestamps();
        });

        // Transport assignments for students
        Schema::create('transport_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('transport_id')->constrained()->onDelete('cascade');
            $table->string('pickup_stop');
            $table->string('drop_stop');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();

            $table->unique(['student_id', 'transport_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transport_assignments');
        Schema::dropIfExists('transports');
    }
};
