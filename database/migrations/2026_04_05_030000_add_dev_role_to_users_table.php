<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Fix for PostgreSQL Check Constraint on Enum
        // Laravel's enum on pgsql creates a check constraint named "users_role_check"
        
        DB::statement('ALTER TABLE "users" DROP CONSTRAINT IF EXISTS "users_role_check"');
        
        DB::statement('ALTER TABLE "users" ADD CONSTRAINT "users_role_check" CHECK (role IN (\'SPV\', \'CPM\', \'IT\', \'PIC\', \'Vendor\', \'DEV\'))');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE "users" DROP CONSTRAINT IF EXISTS "users_role_check"');
        
        DB::statement('ALTER TABLE "users" ADD CONSTRAINT "users_role_check" CHECK (role IN (\'SPV\', \'CPM\', \'IT\', \'PIC\', \'Vendor\'))');
    }
};
