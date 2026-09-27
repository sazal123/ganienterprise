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
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'flashsale')) {
                $table->tinyInteger('flashsale')->nullable()->default(0)->after('status');
            }
            if (!Schema::hasColumn('products', 'sold')) {
                $table->integer('sold')->nullable()->default(0)->after('stock');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['flashsale', 'sold']);
        });
    }
};
