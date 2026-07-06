<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('failure_tracker', function (Blueprint $table) {
            $table->id('failure_id');
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('task_id');
            $table->integer('consecutive_failures')->default(0);
            $table->timestamp('last_failure_date')->nullable();

            $table->unique(['student_id', 'task_id']);

            $table->foreign('student_id')
                  ->references('user_id')->on('users')
                  ->onDelete('cascade');

            $table->foreign('task_id')
                  ->references('task_id')->on('tasks')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('failure_tracker');
    }
};
