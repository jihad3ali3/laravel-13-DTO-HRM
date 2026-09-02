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
        Schema::create('performance_goals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees');
            $table->string('title');
            $table->string('description')->nullable();
            $table->date('start_date');
            $table->date('due_date');
            $table->unsignedTinyInteger('progress')->default(0);
            $table->enum('priority', ['low', 'medium', 'high'])->default('low');
            $table->enum('status', ['not_started', 'in_progress', 'completed', 'cancelled'])->default('not_started');
            $table->timestamps();
        });

        Schema::create('performance_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('reviewer_id')->constrained('users')->cascadeOnDelete();
            $table->string('review_cycle'); // 1 to 5
            $table->timestamp('reviewed_at')->nullable();
            $table->unsignedTinyInteger('rating')->default(0); // 1 to 5
            $table->text('strengths')->nullable();
            $table->text('area_of_improvements')->nullable();
            $table->text('employee_feedback')->nullable();
            $table->enum('status', ['draft', 'submitted', 'acknowledge'])->default('draft');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('performance_goals');
        Schema::dropIfExists('performance_reviews');
    }
};
