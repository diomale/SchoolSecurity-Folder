<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'mysql_second';

    public function up(): void
    {
        Schema::connection('mysql_second')->table('events', function (Blueprint $table) {
            $table->date('event_end_date')->nullable()->after('event_date');
        });
    }

    public function down(): void
    {
        Schema::connection('mysql_second')->table('events', function (Blueprint $table) {
            $table->dropColumn('event_end_date');
        });
    }
};
