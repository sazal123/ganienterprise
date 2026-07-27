<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('stories', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->string('video');
            $table->string('thumbnail')->nullable();
            $table->unsignedBigInteger('product_id')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->integer('order_id')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('stories');
    }
};
