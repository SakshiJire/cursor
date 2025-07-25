<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hostels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institute_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->enum('type', ['boys', 'girls', 'mixed']);
            $table->string('address');
            $table->foreignId('warden_id')->nullable()->constrained('users')->onDelete('set null');
            $table->integer('total_rooms');
            $table->integer('total_beds');
            $table->decimal('monthly_fee', 8, 2);
            $table->decimal('security_deposit', 8, 2)->default(0);
            $table->json('facilities')->nullable(); // Array of facilities
            $table->text('rules')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hostels');
    }
};