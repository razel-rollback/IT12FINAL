<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('blocked_ips', function (Blueprint $table) {
            $table->id();
            $table->string('ip_address')->unique();
            $table->string('reason')->nullable();
            $table->timestamp('blocked_until')->nullable();
            $table->unsignedBigInteger('blocked_by')->nullable();
            $table->foreign('blocked_by')
                  ->references('User_ID')
                  ->on('users')
                  ->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blocked_ips');
    }
};