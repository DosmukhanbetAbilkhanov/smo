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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name_ru')->index();
            $table->string('name_kz')->index();

            $table->foreignId('nomenclature_id')->index()->constrained('nomenclatures');
            $table->foreignId('shop_id')->constrained('shops');
            $table->string('SKU')->nullable();

            $table->decimal('price', 12, 2);
            $table->integer('quantity')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
