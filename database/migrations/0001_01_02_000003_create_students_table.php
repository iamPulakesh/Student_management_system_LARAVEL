<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('student_number', 50)->unique();
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->string('phone', 30)->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('gender', 30)->nullable();
            $table->text('address')->nullable();
            $table->foreignId('department_id')->constrained('departments')->restrictOnDelete();
            $table->foreignId('user_id')->unique()->constrained('users')->restrictOnDelete();
            $table->unsignedSmallInteger('year_of_study');
            $table->timestamps();

            $table->index(['last_name', 'first_name']);
            $table->index('year_of_study');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
