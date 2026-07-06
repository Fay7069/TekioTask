<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id('task_id');
            $table->unsignedBigInteger('routine_id');
            $table->string('title', 200);
            $table->integer('estimated_duration_seconds')->default(120);
            $table->boolean('has_micro_steps')->default(false);
            $table->integer('display_order')->default(1);
            $table->timestamps();

            $table->foreign('routine_id')
                  ->references('routine_id')->on('routines')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
