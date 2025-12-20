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
        Schema::table('units', function (Blueprint $table) {
            $table->string('shortname_ru')->nullable()->after('name_ru');
            $table->string('shortname_kz')->nullable()->after('name_kz');
        });

        // Populate shortnames for existing units
        $this->populateShortnames();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('units', function (Blueprint $table) {
            $table->dropColumn(['shortname_ru', 'shortname_kz']);
        });
    }

    /**
     * Populate shortnames for existing units.
     */
    private function populateShortnames(): void
    {
        $units = [
            ['name_ru' => 'Штука', 'shortname_ru' => 'шт', 'shortname_kz' => 'дн'],
            ['name_ru' => 'Килограмм', 'shortname_ru' => 'кг', 'shortname_kz' => 'кг'],
            ['name_ru' => 'Тонна', 'shortname_ru' => 'т', 'shortname_kz' => 'т'],
            ['name_ru' => 'Метр', 'shortname_ru' => 'м', 'shortname_kz' => 'м'],
            ['name_ru' => 'Метр квадратный', 'shortname_ru' => 'м²', 'shortname_kz' => 'м²'],
        ];

        foreach ($units as $unit) {
            DB::table('units')
                ->where('name_ru', $unit['name_ru'])
                ->update([
                    'shortname_ru' => $unit['shortname_ru'],
                    'shortname_kz' => $unit['shortname_kz'],
                ]);
        }
    }
};
