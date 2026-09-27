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
        Schema::create('telegram_reply_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('telegram_chat_id');
            $table->foreignId('conversation_id')->constrained('conversations')->cascadeOnDelete();
            $table->timestamp('expires_at');
            $table->timestamps();

            // Explicit index
            $table->index('telegram_chat_id');
            $table->index('conversation_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('telegram_reply_sessions');
    }
};
