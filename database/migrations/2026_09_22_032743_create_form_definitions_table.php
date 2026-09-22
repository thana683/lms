<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Admin-definable forms (§1.1 "แบบฟอร์มบันทึกการเรียน", §1.4.3 "บันทึกผลปฏิบัติ")
        // instead of a fixed-column table per form type.
        Schema::create('form_definitions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type'); // topic_log, practice_log, ...
            $table->json('schema'); // field definitions (label, input type, required, ...)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('form_definitions');
    }
};
