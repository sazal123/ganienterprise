<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('payment_histories', function (Blueprint $table) {
            $table->id();
            $table->integer('order_id');
            $table->integer('customer_id')->nullable();
            $table->decimal('amount', 12, 2);
            $table->string('payment_method', 55)->nullable();
            $table->string('trx_id', 55)->nullable();
            $table->string('sender_number', 55)->nullable();
            $table->string('note')->nullable();
            $table->date('payment_date');
            $table->string('received_by')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('payment_histories');
    }
};
