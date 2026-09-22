<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_term_id')->constrained()->cascadeOnDelete();
            $table->string('type'); // objective (auto-graded), subjective (manually graded)
            $table->unsignedTinyInteger('pass_score');
            $table->unsignedTinyInteger('max_attempts')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exams');
    }
};
