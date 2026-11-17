<?php

/**
 * Creates the courses table.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('code', 30)->unique();
            $table->string('name');
            $table->unsignedSmallInteger('credits');
            $table->unsignedTinyInteger('semester')->default(1);
            $table->foreignId('department_id')->constrained('departments')->restrictOnDelete();
            $table->foreignId('teacher_id')->nullable()->constrained('teachers')->restrictOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
