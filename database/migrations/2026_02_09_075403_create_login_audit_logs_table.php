<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('login_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->unsignedBigInteger('user_id')->nullable();
            
            $table->string('status'); // 'success', 'failed', 'locked'
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->string('failure_reason')->nullable(); // 'invalid_credentials', 'account_locked', etc.
            $table->integer('attempts_count')->default(1);
            $table->timestamp('locked_until')->nullable();
            $table->timestamps();
            
            $table->foreign('user_id')->references('User_ID')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('login_audit_logs');
    }
};