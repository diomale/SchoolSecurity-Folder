<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'mysql_second';

    public function up(): void
    {
        Schema::connection('mysql_second')->table('outside_user', function (Blueprint $table) {
            $table->timestamp('terms_accepted_at')->nullable()->after('email_verified_at');
        });

        Schema::connection('mysql_second')->table('inside_user', function (Blueprint $table) {
            $table->timestamp('terms_accepted_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::connection('mysql_second')->table('outside_user', function (Blueprint $table) {
            $table->dropColumn('terms_accepted_at');
        });

        Schema::connection('mysql_second')->table('inside_user', function (Blueprint $table) {
            $table->dropColumn('terms_accepted_at');
        });
    }
};
