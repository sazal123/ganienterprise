<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained('conversations')->cascadeOnDelete();
            $table->enum('sender_type', ['guest', 'customer', 'ai', 'admin', 'system']);
            $table->unsignedBigInteger('sender_id')->nullable();
            $table->enum('message_type', ['text', 'image', 'file', 'system', 'tool'])->default('text');
            $table->text('message');
            $table->json('metadata')->nullable();
            $table->string('telegram_message_id')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            // Explicit indexes
            $table->index('conversation_id');
            $table->index('created_at');
            $table->index(['conversation_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('messages');
    }
};
