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
        Schema::table('nomenclatures', function (Blueprint $table) {
            // Drop the old boolean status column
            $table->dropColumn('status');
        });

        Schema::table('nomenclatures', function (Blueprint $table) {
            // Add new enum status column
            $table->enum('status', ['draft', 'pending', 'approved', 'rejected'])
                ->default('pending')
                ->after('brandname');
            $table->foreignId('approved_by')
                ->nullable()
                ->after('status')
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamp('approved_at')
                ->nullable()
                ->after('approved_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nomenclatures', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropColumn(['status', 'approved_by', 'approved_at']);
        });

        Schema::table('nomenclatures', function (Blueprint $table) {
            // Restore the old boolean status column
            $table->boolean('status')->default(true);
        });
    }
};
