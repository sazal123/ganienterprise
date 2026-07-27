<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('notices', function (Blueprint $table) {
            $table->id();
            $table->string('text');
            $table->string('link')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->integer('order_id')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('notices');
    }
};
