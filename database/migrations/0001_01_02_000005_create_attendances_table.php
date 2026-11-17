<?php

/**
 * Creates the attendances table.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->restrictOnDelete();
            $table->foreignId('course_id')->constrained('courses')->restrictOnDelete();
            $table->date('attendance_date')->index();
            $table->string('status', 20);
            $table->foreignId('recorded_by_user_id')->constrained('users')->restrictOnDelete();
            $table->timestamps();

            $table->unique(['student_id', 'course_id', 'attendance_date'], 'attendances_student_course_date_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
