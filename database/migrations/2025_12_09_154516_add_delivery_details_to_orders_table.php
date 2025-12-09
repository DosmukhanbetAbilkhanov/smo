<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('delivery_entry')->nullable()->after('delivery_address');
            $table->string('delivery_floor')->nullable()->after('delivery_entry');
            $table->string('delivery_apartment')->nullable()->after('delivery_floor');
            $table->string('delivery_intercom')->nullable()->after('delivery_apartment');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['delivery_entry', 'delivery_floor', 'delivery_apartment', 'delivery_intercom']);
        });
    }
};
