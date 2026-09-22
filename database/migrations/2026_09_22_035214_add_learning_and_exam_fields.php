<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('topic_progress', function (Blueprint $table) {
            $table->timestamp('last_seen_at')->nullable()->after('course_topic_id'); // presence heartbeat, §1.4.1
        });

        Schema::table('course_topics', function (Blueprint $table) {
            $table->boolean('requires_practice_log')->default(false)->after('has_quiz'); // §1.4.3
        });

        Schema::table('exams', function (Blueprint $table) {
            $table->unsignedSmallInteger('question_count')->nullable()->after('type'); // §1.5.1, informational
        });
    }

    public function down(): void
    {
        Schema::table('topic_progress', function (Blueprint $table) {
            $table->dropColumn('last_seen_at');
        });

        Schema::table('course_topics', function (Blueprint $table) {
            $table->dropColumn('requires_practice_log');
        });

        Schema::table('exams', function (Blueprint $table) {
            $table->dropColumn('question_count');
        });
    }
};
