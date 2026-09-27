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
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();

            $table->unsignedInteger('customer_id')->nullable();
            $table->foreign('customer_id')->references('id')->on('customers')->nullOnDelete();

            $table->string('guest_token_hash', 64)->nullable();
            $table->enum('status', ['open', 'closed'])->default('open');
            $table->enum('mode', ['ai', 'human'])->default('ai');

            $table->unsignedInteger('assigned_admin_id')->nullable();
            $table->foreign('assigned_admin_id')->references('id')->on('users')->nullOnDelete();

            $table->string('subject')->nullable();
            $table->timestamp('last_message_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            // Explicit indexes
            $table->index('user_id');
            $table->index('customer_id');
            $table->index('guest_token_hash');
            $table->index('status');
            $table->index('last_message_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('conversations');
    }
};
