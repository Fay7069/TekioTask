<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('group_members', function (Blueprint $table) {
            $table->id('member_id');
            $table->unsignedBigInteger('group_id');
            $table->unsignedBigInteger('student_id');

            $table->foreign('group_id')
                  ->references('group_id')->on('student_groups')
                  ->onDelete('cascade');

            $table->foreign('student_id')
                  ->references('user_id')->on('users')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('group_members');
    }
};
