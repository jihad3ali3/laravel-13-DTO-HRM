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
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('content')->nullable();
            $table->foreignId('department_id')->nullable()->constrained('departments');
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->enum('priority', ['low', 'medium', 'high'])->default('low');
            $table->boolean('is_pinned')->default(false);
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->boolean('is_published')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('announcements');
    }
};
