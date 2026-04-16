<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schools', function (Blueprint $table) {
            $table->id();
            $table->string('name', 200);
            $table->string('code', 20)->unique();
            $table->string('street')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('pincode', 10)->nullable();
            $table->string('country')->default('India');
            $table->string('email')->unique();
            $table->string('phone', 15);
            $table->string('logo_url')->nullable();
            $table->string('logo_s3_key')->nullable();
            $table->time('entry_time')->default('08:00:00');
            $table->time('late_threshold')->default('08:30:00');
            $table->time('exit_time')->default('14:00:00');
            $table->unsignedBigInteger('active_template_id')->nullable();
            $table->enum('subscription_status', [
                'active', 'expired', 'trial', 'none'
            ])->default('none');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index('code');
            $table->index('subscription_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schools');
    }
};
