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
        if (!Schema::hasColumn('ai_usage_logs', 'response_time_ms')) {
            Schema::table('ai_usage_logs', function (Blueprint $table) {
                $table->integer('response_time_ms')->nullable()->after('estimated_cost');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('ai_usage_logs', 'response_time_ms')) {
            Schema::table('ai_usage_logs', function (Blueprint $table) {
                $table->dropColumn('response_time_ms');
            });
        }
    }
};
