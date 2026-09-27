<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'is_new')) {
                if (Schema::hasColumn('products', 'flashsale')) {
                    $table->tinyInteger('is_new')->nullable()->after('flashsale');
                } else {
                    $table->tinyInteger('is_new')->nullable();
                }
            }
            if (!Schema::hasColumn('products', 'is_prime')) {
                $table->tinyInteger('is_prime')->nullable()->after('is_new');
            }
        });
    }

    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['is_new', 'is_prime']);
        });
    }
};
