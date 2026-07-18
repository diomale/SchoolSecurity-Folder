<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'mysql_second';

    public function up(): void
    {
        Schema::connection('mysql_second')->table('outside_user', function ($table) {
            $table->timestamp('email_verified_at')->nullable()->after('status');
            $table->string('email_verification_token', 100)->nullable()->after('email_verified_at');
        });
    }

    public function down(): void
    {
        Schema::connection('mysql_second')->table('outside_user', function ($table) {
            $table->dropColumn(['email_verified_at', 'email_verification_token']);
        });
    }
};
