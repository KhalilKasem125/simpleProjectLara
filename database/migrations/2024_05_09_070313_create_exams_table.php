<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
           // $table->integer('subject_id')->unsigned();
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
            $table->string("Exam_Name");
            $table->integer('exam_time');
            $table->integer('qestions_number');
            $table->integer('success_degree');
            $table->date('exam_day_start_point');
            $table->date('exam_day_end_point');
            $table->integer('created_by')->unsigned();
            $table->integer('updated_by')->unsigned()->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exams');
    }
};
