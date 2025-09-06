<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeaderboardsTable extends Migration
{
    public function up()
    {
        Schema::create('leader_boards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('leader_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('leader_type');
            $table->float('points')->default(0);
            $table->timestamps();
            $table->index(['leader_id', 'leader_type']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('leaderboards');
    }
}
