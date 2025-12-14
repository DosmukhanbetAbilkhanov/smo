<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add bilingual columns
        Schema::table('cities', function (Blueprint $table) {
            $table->string('name_ru')->after('name')->nullable();
            $table->string('name_kz')->after('name_ru')->nullable();
        });

        // Migrate existing data from 'name' to both bilingual columns
        DB::table('cities')->get()->each(function ($city) {
            DB::table('cities')
                ->where('id', $city->id)
                ->update([
                    'name_ru' => $city->name,
                    'name_kz' => $city->name,
                ]);
        });

        // Make columns non-nullable and drop old column
        Schema::table('cities', function (Blueprint $table) {
            $table->string('name_ru')->nullable(false)->change();
            $table->string('name_kz')->nullable(false)->change();
            $table->dropColumn('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate 'name' column
        Schema::table('cities', function (Blueprint $table) {
            $table->string('name')->after('id');
        });

        // Migrate data back from name_ru to name
        DB::table('cities')->get()->each(function ($city) {
            DB::table('cities')
                ->where('id', $city->id)
                ->update(['name' => $city->name_ru]);
        });

        // Drop bilingual columns
        Schema::table('cities', function (Blueprint $table) {
            $table->dropColumn(['name_ru', 'name_kz']);
        });
    }
};
