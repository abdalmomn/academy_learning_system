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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->decimal('amount',10,2);
            $table->text('description');
            $table->enum('status',['pending', 'completed', 'failed', 'refunded']);
            $table->enum('transaction_type',['credit', 'debit']);
            $table->enum('transaction_method',['card', 'paypal', 'bank', 'referral', 'manual', 'admin', 'reward', 'course_purchase']);
            $table->foreignId('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
