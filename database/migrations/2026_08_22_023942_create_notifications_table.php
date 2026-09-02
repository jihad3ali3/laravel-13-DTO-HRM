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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees');
            $table->string('type'); // general
            $table->string('title');
            $table->text('message');
            $table->string('channel')->default('database');
            $table->enum('status', ['pending', 'sent', 'delivered', 'read'])->default('pending');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index(['employee_id', 'read_at']);
            $table->index(['type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
