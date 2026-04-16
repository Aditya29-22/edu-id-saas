<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('school_id')->nullable();
            $table->enum('type', ['system', 'custom'])->default('custom');
            $table->string('front_image_url')->nullable();
            $table->string('front_image_s3_key')->nullable();
            $table->string('back_image_url')->nullable();
            $table->string('back_image_s3_key')->nullable();
            $table->json('layout')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->index('school_id');
        });

        Schema::table('schools', function (Blueprint $table) {
            $table->foreign('active_template_id')
                  ->references('id')
                  ->on('templates')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('schools', function (Blueprint $table) {
            $table->dropForeign(['active_template_id']);
        });
        Schema::dropIfExists('templates');
    }
};
