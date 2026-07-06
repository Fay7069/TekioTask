<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('progress_logs', function (Blueprint $table) {
            $table->id('log_id');
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('task_id');
            $table->enum('status', ['completed', 'failed', 'skipped']);
            $table->integer('time_taken_seconds')->nullable();
            $table->timestamp('attempt_timestamp')->useCurrent();
            $table->boolean('was_adapted')->default(false);

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
        Schema::dropIfExists('progress_logs');
    }
};
