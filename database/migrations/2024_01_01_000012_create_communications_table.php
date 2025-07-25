<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chat_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('type', ['public', 'private', 'class', 'staff', 'parent']);
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('class_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('avatar')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('chat_group_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained('chat_groups')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('role', ['admin', 'moderator', 'member'])->default('member');
            $table->timestamp('joined_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->unique(['group_id', 'user_id']);
        });

        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sender_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('receiver_id')->nullable()->constrained('users')->onDelete('cascade'); // For private messages
            $table->foreignId('group_id')->nullable()->constrained('chat_groups')->onDelete('cascade'); // For group messages
            $table->text('content');
            $table->enum('type', ['text', 'image', 'file', 'voice', 'announcement']);
            $table->string('file_path')->nullable();
            $table->string('file_name')->nullable();
            $table->integer('file_size')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->boolean('is_deleted')->default(false);
            $table->foreignId('replied_to')->nullable()->constrained('messages')->onDelete('set null');
            $table->timestamps();
        });

        Schema::create('message_reactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('message_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('reaction'); // emoji or reaction type
            $table->timestamps();
            
            $table->unique(['message_id', 'user_id', 'reaction']);
        });

        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('message');
            $table->enum('type', ['info', 'warning', 'success', 'error', 'announcement']);
            $table->string('icon')->nullable();
            $table->json('data')->nullable(); // Additional data
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->string('action_url')->nullable();
            $table->timestamps();
        });

        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('content');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->enum('target_audience', ['all', 'students', 'parents', 'staff', 'class_specific']);
            $table->foreignId('class_id')->nullable()->constrained()->onDelete('cascade');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->date('publish_date')->nullable();
            $table->date('expire_date')->nullable();
            $table->boolean('is_published')->default(false);
            $table->string('attachment')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('announcements');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('message_reactions');
        Schema::dropIfExists('messages');
        Schema::dropIfExists('chat_group_members');
        Schema::dropIfExists('chat_groups');
    }
};