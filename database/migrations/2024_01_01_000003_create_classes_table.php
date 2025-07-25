<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Playgroup, Nursery, Jr.KG, Sr.KG
            $table->string('code')->unique(); // PG, NUR, JKG, SKG
            $table->text('description')->nullable();
            $table->integer('capacity')->default(25);
            $table->decimal('monthly_fee', 10, 2)->default(0);
            $table->decimal('admission_fee', 10, 2)->default(0);
            $table->json('subjects')->nullable(); // Store subjects for the class
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('classes');
    }
};