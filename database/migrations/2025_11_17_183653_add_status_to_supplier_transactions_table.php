<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('supplier_transactions', function (Blueprint $table) {
            // Only add the status column if it doesn't exist
            if (!Schema::hasColumn('supplier_transactions', 'status')) {
                $table->enum('status', ['pending', 'completed', 'cancelled', 'paid'])->default('pending');
            }
        });
    }

    public function down(): void
    {
        Schema::table('supplier_transactions', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};