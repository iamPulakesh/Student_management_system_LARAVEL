<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->restrictOnDelete();
            $table->foreignId('course_id')->constrained('courses')->restrictOnDelete();
            $table->string('assessment_name', 150);
            $table->decimal('marks_obtained', 6, 2);
            $table->decimal('max_marks', 6, 2);
            $table->date('assessment_date')->nullable();
            $table->foreignId('recorded_by_user_id')->constrained('users')->restrictOnDelete();
            $table->timestamps();

            $table->unique(['student_id', 'course_id', 'assessment_name'], 'assessments_student_course_name_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assessments');
    }
};
