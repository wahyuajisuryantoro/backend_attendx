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
        Schema::create('leave_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('type', ['annual', 'sick', 'emergency', 'other']);
            $table->text('reason');
            $table->enum('status', ['pending', 'approved', 'rejected', 'canceled'])->default('pending');
            $table->foreignId('approved_by')->nullable()->references('id')->on('users');
            $table->text('rejection_reason')->nullable();
            $table->string('attachment')->nullable(); // For doctor's notes, etc.
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_requests');
    }
};
