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
        Schema::table('sales_transactions', function (Blueprint $table) {
            // Drop the foreign key constraint first
            $table->dropForeign(['Customer_ID']);
            
            // Modify the column to be nullable
            $table->unsignedBigInteger('Customer_ID')->nullable()->change();
            
            // Re-add the foreign key constraint
            $table->foreign('Customer_ID')
                  ->references('Customer_ID')
                  ->on('customers')
                  ->onDelete('set null'); // Set to null if customer is deleted
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales_transactions', function (Blueprint $table) {
            // Drop the foreign key constraint
            $table->dropForeign(['Customer_ID']);
            
            // Change back to NOT nullable
            $table->unsignedBigInteger('Customer_ID')->nullable(false)->change();
            
            // Re-add the foreign key constraint
            $table->foreign('Customer_ID')
                  ->references('Customer_ID')
                  ->on('customers')
                  ->onDelete('cascade');
        });
    }
};