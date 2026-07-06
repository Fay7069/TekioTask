<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_groups', function (Blueprint $table) {
            $table->id('group_id');
            $table->string('group_name', 100);
            $table->unsignedBigInteger('teacher_id');
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('teacher_id')
                  ->references('user_id')->on('users')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_groups');
    }
};
