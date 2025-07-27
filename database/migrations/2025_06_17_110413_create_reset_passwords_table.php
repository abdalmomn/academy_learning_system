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
        Schema::create('reset_passwords', function (Blueprint $table) {
            $table->id();
<<<<<<< HEAD
            $table->string('email')->index();
            $table->string('token')->unique();
            $table->string('reset_code');
            $table->timestamp('code_expires_at')->nullable();
            $table->timestamp('token_expires_at')->nullable();
=======
            $table->string('email');
            $table->integer('code');
>>>>>>> ca7ced0 (first version: database, models and spatie role)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reset_passwords');
    }
};
