<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('stock_edit_history', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('cashier_id');
            $table->string('action'); // stock_in, stock_out, adjustment, update, create, delete
            $table->string('field_changed')->nullable();
            $table->string('old_value')->nullable();
            $table->string('new_value')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();

            // Add indexes for better query performance
            $table->index('product_id');
            $table->index('cashier_id');
            $table->index('action');
            $table->index('created_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('stock_edit_history');
    }
};