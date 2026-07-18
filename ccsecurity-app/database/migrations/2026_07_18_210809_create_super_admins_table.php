<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('super_admins', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('password', 100)->nullable();
            $table->string('status', 145)->nullable();
            $table->string('remember_token', 145)->nullable();
            $table->string('updated_at', 145)->nullable();
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('super_admins');
    }
};
