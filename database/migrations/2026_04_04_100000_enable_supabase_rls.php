<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Disable transaction for this migration to avoid "current transaction is aborted" errors in PostgreSQL.
     */
    public $withinTransaction = false;

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Tables mentioned in the Supabase Security Advisor
        $tables = [
            'users',
            'berita_acaras',
            'asset_findings',
            'password_reset_tokens',
            'sessions',
            'cache',
            'cache_locks',
            'jobs',
            'job_batches',
            'failed_jobs'
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                DB::statement("ALTER TABLE \"$table\" ENABLE ROW LEVEL SECURITY;");
                // Also ensure no public access via postgrest anon role
                DB::statement("ALTER TABLE \"$table\" FORCE ROW LEVEL SECURITY;");
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = [
            'users',
            'berita_acaras',
            'asset_findings',
            'password_reset_tokens',
            'sessions',
            'cache',
            'cache_locks',
            'jobs',
            'job_batches',
            'failed_jobs',
            'migrations'
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                DB::statement("ALTER TABLE \"$table\" DISABLE ROW LEVEL SECURITY;");
            }
        }
    }
};
