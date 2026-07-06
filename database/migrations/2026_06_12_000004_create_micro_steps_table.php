<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('micro_steps', function (Blueprint $table) {
            $table->id('step_id');
            $table->unsignedBigInteger('task_id');
            $table->integer('step_order');
            $table->text('description');
            $table->string('image_url', 255)->nullable();

            $table->foreign('task_id')
                  ->references('task_id')->on('tasks')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('micro_steps');
    }
};
