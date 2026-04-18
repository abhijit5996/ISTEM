<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('bookings', 'phone')) {
                $table->string('phone', 20)->nullable()->after('email');
            }

            if (!Schema::hasColumn('bookings', 'admin_comment')) {
                $table->string('admin_comment', 500)->nullable()->after('status');
            }

            if (!Schema::hasColumn('bookings', 'rejection_reason')) {
                $table->string('rejection_reason', 500)->nullable()->after('admin_comment');
            }
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $columns = [];

            if (Schema::hasColumn('bookings', 'rejection_reason')) {
                $columns[] = 'rejection_reason';
            }

            if (Schema::hasColumn('bookings', 'admin_comment')) {
                $columns[] = 'admin_comment';
            }

            if (Schema::hasColumn('bookings', 'phone')) {
                $columns[] = 'phone';
            }

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }
};
