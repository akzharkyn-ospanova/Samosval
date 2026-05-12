<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasColumn('leads', 'assigned_to')) {
            return;
        }

        $constraintName = DB::table('information_schema.KEY_COLUMN_USAGE')
            ->where('TABLE_SCHEMA', DB::getDatabaseName())
            ->where('TABLE_NAME', 'leads')
            ->where('COLUMN_NAME', 'assigned_to')
            ->whereNotNull('REFERENCED_TABLE_NAME')
            ->value('CONSTRAINT_NAME');

        Schema::table('leads', function (Blueprint $table) use ($constraintName) {
            if ($constraintName) {
                $table->dropForeign($constraintName);
            }

            $table->foreign('assigned_to')
                ->references('id')
                ->on('staff_members')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        if (!Schema::hasColumn('leads', 'assigned_to')) {
            return;
        }

        $constraintName = DB::table('information_schema.KEY_COLUMN_USAGE')
            ->where('TABLE_SCHEMA', DB::getDatabaseName())
            ->where('TABLE_NAME', 'leads')
            ->where('COLUMN_NAME', 'assigned_to')
            ->whereNotNull('REFERENCED_TABLE_NAME')
            ->value('CONSTRAINT_NAME');

        Schema::table('leads', function (Blueprint $table) use ($constraintName) {
            if ($constraintName) {
                $table->dropForeign($constraintName);
            }

            $table->foreign('assigned_to')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
        });
    }
};
