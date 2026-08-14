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
            $table->dropIndex('idx_inside_user_qr');
            $table->unique('qr_value', 'uq_inside_user_qr');
        });

        Schema::connection('mysql_second')->table('outside_user', function (Blueprint $table) {
            $table->dropIndex('idx_outside_user_qr');
            $table->unique('qr_value', 'uq_outside_user_qr');
        });
    }

    public function down(): void
    {
        Schema::connection('mysql_second')->table('inside_user', function (Blueprint $table) {
            $table->dropUnique('uq_inside_user_qr');
            $table->index('qr_value', 'idx_inside_user_qr');
        });

        Schema::connection('mysql_second')->table('outside_user', function (Blueprint $table) {
            $table->dropUnique('uq_outside_user_qr');
            $table->index('qr_value', 'idx_outside_user_qr');
        });
    }
};
