<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('case_notes', function (Blueprint $table) {
            $table->id('note_id');
            $table->unsignedBigInteger('therapist_id');
            $table->unsignedBigInteger('student_id');
            $table->text('content');
            $table->timestamps();

            $table->foreign('therapist_id')
                  ->references('user_id')->on('users')
                  ->onDelete('cascade');

            $table->foreign('student_id')
                  ->references('user_id')->on('users')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('case_notes');
    }
};
