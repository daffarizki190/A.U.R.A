<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
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
            'migrations'
        ];

        foreach ($tables as $table) {
            DB::statement("ALTER TABLE \"$table\" ENABLE ROW LEVEL SECURITY;");
            // Also ensure no public access via postgrest anon role
            DB::statement("ALTER TABLE \"$table\" FORCE ROW LEVEL SECURITY;");
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
            'migrations'
        ];

        foreach ($tables as $table) {
            DB::statement("ALTER TABLE \"$table\" DISABLE ROW LEVEL SECURITY;");
        }
    }
};
