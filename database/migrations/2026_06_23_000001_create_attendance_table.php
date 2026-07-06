<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance', function (Blueprint $table) {
            $table->id('attendance_id');
            $table->unsignedBigInteger('student_id');
            $table->date('attendance_date');
            $table->time('checked_in_at');
            $table->timestamps();

            $table->foreign('student_id')
                  ->references('user_id')
                  ->on('users')
                  ->onDelete('cascade');

            // One check-in per student per day
            $table->unique(['student_id', 'attendance_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance');
    }
};
