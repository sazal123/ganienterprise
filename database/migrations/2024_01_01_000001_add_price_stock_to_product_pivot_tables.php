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
        Schema::table('productcolors', function (Blueprint $table) {
            $table->decimal('price', 10, 2)->nullable()->after('color_id');
            $table->integer('stock')->nullable()->after('price');
        });

        Schema::table('productsizes', function (Blueprint $table) {
            $table->decimal('price', 10, 2)->nullable()->after('size_id');
            $table->integer('stock')->nullable()->after('price');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('productcolors', function (Blueprint $table) {
            $table->dropColumn(['price', 'stock']);
        });

        Schema::table('productsizes', function (Blueprint $table) {
            $table->dropColumn(['price', 'stock']);
        });
    }
};
