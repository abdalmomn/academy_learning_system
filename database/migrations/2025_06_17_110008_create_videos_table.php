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
        Schema::create('videos', function (Blueprint $table) {
            $table->id();
            $table->text('description')->nullable();
            $table->time('duration')->nullable();
            $table->string('url');
            $table->string('title');
            $table->string('poster');
            $table->foreignId('course_id')->references('id')->on('courses')->onDelete('cascade');
            $table->boolean('is_comments_locked')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('videos');
    }
};
