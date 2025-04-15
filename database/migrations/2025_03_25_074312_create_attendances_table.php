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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->time('clock_in')->nullable();
            $table->time('clock_out')->nullable();
            $table->enum('clock_in_method', ['fingerprint', 'face_recognition'])->nullable();
            $table->enum('clock_out_method', ['fingerprint', 'face_recognition'])->nullable();
            $table->string('clock_in_photo')->nullable();
            $table->string('clock_out_photo')->nullable();
            $table->string('clock_in_location')->nullable(); 
            $table->string('clock_out_location')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        
            $table->unique(['user_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
