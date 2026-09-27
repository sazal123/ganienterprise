<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'order_date')) {
                if (Schema::hasColumn('orders', 'note')) {
                    $table->date('order_date')->nullable()->after('note');
                } else {
                    $table->date('order_date')->nullable();
                }
            }
            if (!Schema::hasColumn('orders', 'delivery_date')) {
                $table->date('delivery_date')->nullable()->after('order_date');
            }
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['order_date', 'delivery_date']);
        });
    }
};
