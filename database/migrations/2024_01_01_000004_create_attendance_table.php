<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('school_id');
            $table->date('date');
            $table->timestamp('entry_time')->nullable();
            $table->unsignedBigInteger('entry_scanned_by')->nullable();
            $table->boolean('is_late')->default(false);
            $table->timestamp('exit_time')->nullable();
            $table->unsignedBigInteger('exit_scanned_by')->nullable();
            $table->enum('status', ['entered', 'exited', 'absent'])->default('entered');
            $table->timestamps();

            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->foreign('entry_scanned_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('exit_scanned_by')->references('id')->on('users')->nullOnDelete();
            $table->unique(['student_id', 'date']);
            $table->index(['school_id', 'date']);
        });

        Schema::create('attendance_scan_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('attendance_id');
            $table->enum('action', ['entry', 'exit']);
            $table->timestamp('scanned_at');
            $table->unsignedBigInteger('scanned_by')->nullable();
            $table->timestamps();

            $table->foreign('attendance_id')->references('id')->on('attendance')->onDelete('cascade');
            $table->foreign('scanned_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_scan_logs');
        Schema::dropIfExists('attendance');
    }
};
