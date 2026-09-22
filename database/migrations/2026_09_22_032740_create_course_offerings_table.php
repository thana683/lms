<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // A branch's approved (or pending) request to run a course template (§1.2).
        // Quota and dates live here, not on the template, since they vary per branch/run.
        Schema::create('course_offerings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('requested_by')->constrained('users');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status')->default('pending'); // pending, approved, rejected
            $table->unsignedInteger('quota'); // User License Quota
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->json('session_dates')->nullable(); // materialized concrete dates from course.schedule_rule
            $table->string('attachment_path')->nullable(); // signed approval letter
            $table->timestamp('decided_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_offerings');
    }
};
