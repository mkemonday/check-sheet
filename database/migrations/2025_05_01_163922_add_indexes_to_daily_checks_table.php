<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('daily_checks', function (Blueprint $table) {
            $table->index('check_item_id');
            $table->index('check_date');
            $table->index('status');
            $table->index('checked_by');
            $table->index('confirmed_by');
            $table->index('verified_by');
        });
    }

    public function down(): void
    {
        Schema::table('daily_checks', function (Blueprint $table) {
            $table->dropIndex(['check_item_id']);
            $table->dropIndex(['check_date']);
            $table->dropIndex(['status']);
            $table->dropIndex(['checked_by']);
            $table->dropIndex(['confirmed_by']);
            $table->dropIndex(['verified_by']);
        });
    }
};

