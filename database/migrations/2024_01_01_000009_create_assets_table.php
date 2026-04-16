<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('school_id');
            $table->unsignedBigInteger('uploaded_by')->nullable();
            $table->enum('type', [
                'student_photo', 'template', 'id_card', 'logo', 'qr_code', 'other'
            ]);
            $table->string('original_name');
            $table->string('mime_type');
            $table->unsignedBigInteger('size');
            $table->string('s3_key')->unique();
            $table->string('s3_url');
            $table->string('cdn_url')->nullable();
            $table->string('related_model')->nullable();
            $table->unsignedBigInteger('related_id')->nullable();
            $table->string('checksum', 32)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->foreign('uploaded_by')->references('id')->on('users')->nullOnDelete();
            $table->index(['school_id', 'type']);
            $table->index('checksum');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
