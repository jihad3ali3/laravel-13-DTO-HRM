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
        Schema::create('expenses_claims', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees');
            $table->string('claim_code')->unique();
            $table->string('title');
            $table->string('category')->comment('travel, meals, internet, equipment');
            $table->decimal('amount', 12, 2)->default(0);
            $table->date('expense_date');
            $table->string('description')->nullable();
            $table->string('receipt_file')->nullable();
            $table->enum('status', ['pending', 'accepted', 'rejected', 'reimbursed'])->default('pending');
            $table->foreignId('actioned_by')->nullable()->constrained('users');
            $table->timestamp('actioned_at')->nullable();
            $table->string('reject_reason')->nullable();
            $table->date('reimbursed_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses_claims');
    }
};
