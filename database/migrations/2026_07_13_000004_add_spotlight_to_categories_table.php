<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('categories', function (Blueprint $table) {
            if (!Schema::hasColumn('categories', 'spotlight')) {
                if (Schema::hasColumn('categories', 'front_view')) {
                    $table->tinyInteger('spotlight')->nullable()->after('front_view');
                } else {
                    $table->tinyInteger('spotlight')->nullable();
                }
            }
        });
    }

    public function down()
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('spotlight');
        });
    }
};
