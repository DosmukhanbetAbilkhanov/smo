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
        Schema::create('nomenclatures', function (Blueprint $table) {
            $table->id();
            $table->string('name_ru')->index();
            $table->string('name_kz')->index();
            $table->foreignId('unit_id')->constrained('units');
            $table->foreignId('category_id')->constrained('categories');

            $table->text('description_ru')->nullable();
            $table->text('description_kz')->nullable();

            $table->string('GTIN')->nullable();
            $table->string('NTIN')->nullable();
            $table->string('brandname')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nomenclatures');
    }
};
