<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rewards', function (Blueprint $table) {
            $table->id('reward_id');
            $table->unsignedBigInteger('student_id')->unique();
            $table->integer('points')->default(0);
            $table->json('badges')->nullable();
            $table->timestamp('last_updated')->useCurrent()->useCurrentOnUpdate();

            $table->foreign('student_id')
                  ->references('user_id')->on('users')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rewards');
    }
};
