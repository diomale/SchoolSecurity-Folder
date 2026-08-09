<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('superadmin_activity_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('superadmin_id');
            $table->string('superadmin_name', 200);
            $table->string('category', 50)->index();
            $table->string('action', 100);
            $table->text('description');
            $table->json('metadata')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 500)->nullable();
            $table->timestamp('created_at')->nullable();

            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('superadmin_activity_logs');
    }
};
