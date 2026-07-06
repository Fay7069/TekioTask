<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('routine_assignments', function (Blueprint $table) {
            $table->id('assignment_id');
            $table->unsignedBigInteger('routine_id');
            $table->unsignedBigInteger('student_id')->nullable();
            $table->unsignedBigInteger('group_id')->nullable();
            $table->date('assigned_date');
            $table->boolean('is_active')->default(true);

            $table->foreign('routine_id')
                  ->references('routine_id')->on('routines')
                  ->onDelete('cascade');

            $table->foreign('student_id')
                  ->references('user_id')->on('users')
                  ->onDelete('set null');

            $table->foreign('group_id')
                  ->references('group_id')->on('student_groups')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('routine_assignments');
    }
};
