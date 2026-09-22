<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_topics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_term_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('day_number')->nullable();
            $table->string('title');
            $table->string('content_type'); // video, live, mixed
            $table->string('content_url')->nullable();
            $table->boolean('has_quiz')->default(false);
            $table->unsignedTinyInteger('quiz_pass_score')->nullable();
            $table->unsignedSmallInteger('order_number')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_topics');
    }
};
