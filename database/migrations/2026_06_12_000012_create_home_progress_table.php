<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('home_progress', function (Blueprint $table) {
            $table->id('home_task_id');
            $table->unsignedBigInteger('parent_id');
            $table->unsignedBigInteger('student_id');
            $table->string('task_name', 200);
            $table->date('completed_date');
            $table->timestamp('recorded_at')->useCurrent();

            $table->foreign('parent_id')
                  ->references('user_id')->on('users')
                  ->onDelete('cascade');

            $table->foreign('student_id')
                  ->references('user_id')->on('users')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('home_progress');
    }
};
