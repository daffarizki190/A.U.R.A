<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Fix: Status enum 'Pending Approval' tidak ada di migration asal.
     * Migration asal memiliki: Open, On Progress, Pending, Done
     * Controller menggunakan: 'Pending Approval' — tidak cocok.
     *
     * Solution: Drop CHECK constraint lama dan buat yang baru dengan
     * 'Pending Approval' sebagai pengganti 'Pending'.
     */
    public function up(): void
    {
        // PostgreSQL menggunakan CHECK constraint untuk enum
        // Hapus constraint lama lalu buat yang baru
        DB::statement("ALTER TABLE asset_findings DROP CONSTRAINT IF EXISTS asset_findings_status_check");
        DB::statement("ALTER TABLE asset_findings ADD CONSTRAINT asset_findings_status_check CHECK (status::text = ANY (ARRAY['Open'::text, 'On Progress'::text, 'Pending Approval'::text, 'Done'::text]))");

        // Update data lama: jika ada nilai 'Pending' ubah ke 'Pending Approval'
        DB::statement("UPDATE asset_findings SET status = 'Pending Approval' WHERE status = 'Pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Kembalikan ke enum semula
        DB::statement("UPDATE asset_findings SET status = 'Pending' WHERE status = 'Pending Approval'");
        DB::statement("ALTER TABLE asset_findings DROP CONSTRAINT IF EXISTS asset_findings_status_check");
        DB::statement("ALTER TABLE asset_findings ADD CONSTRAINT asset_findings_status_check CHECK (status::text = ANY (ARRAY['Open'::text, 'On Progress'::text, 'Pending'::text, 'Done'::text]))");
    }
};
