<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('school_id');
            $table->string('student_id', 30)->nullable();
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->string('roll_number', 30);
            $table->string('class_name', 20);
            $table->string('section', 10)->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('blood_group', 5)->nullable();
            $table->string('guardian_name', 200)->nullable();
            $table->string('guardian_phone', 15)->nullable();
            $table->text('address')->nullable();
            $table->string('photo_original_url')->nullable();
            $table->string('photo_original_s3_key')->nullable();
            $table->string('photo_compressed_url')->nullable();
            $table->string('photo_compressed_s3_key')->nullable();
            $table->string('photo_thumbnail_url')->nullable();
            $table->string('photo_thumbnail_s3_key')->nullable();
            $table->text('qr_data')->nullable();
            $table->string('qr_token')->nullable()->unique();
            $table->string('qr_image_url')->nullable();
            $table->string('qr_image_s3_key')->nullable();
            $table->boolean('id_card_generated')->default(false);
            $table->string('id_card_url')->nullable();
            $table->string('id_card_s3_key')->nullable();
            $table->timestamp('id_card_generated_at')->nullable();
            $table->string('academic_year', 10)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->index(['school_id', 'class_name', 'section']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
