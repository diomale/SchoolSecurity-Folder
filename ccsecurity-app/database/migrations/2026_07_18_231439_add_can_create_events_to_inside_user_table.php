<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'mysql_second';

    public function up(): void
    {
        Schema::connection('mysql_second')->table('inside_user', function (Blueprint $table) {
            $table->boolean('can_create_events')->default(false)->after('qr_status');
        });
    }

    public function down(): void
    {
        Schema::connection('mysql_second')->table('inside_user', function (Blueprint $table) {
            $table->dropColumn('can_create_events');
        });
    }
};
