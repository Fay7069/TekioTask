<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id('report_id');
            $table->unsignedBigInteger('generated_by');
            $table->unsignedBigInteger('student_id')->nullable();
            $table->enum('report_type', ['individual', 'class']);
            $table->string('file_url', 255)->nullable();
            $table->timestamp('generated_date')->useCurrent();

            $table->foreign('generated_by')
                  ->references('user_id')->on('users')
                  ->onDelete('cascade');

            $table->foreign('student_id')
                  ->references('user_id')->on('users')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
