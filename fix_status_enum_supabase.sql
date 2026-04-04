-- ============================================================
-- FIX: asset_findings.status enum — Tambah 'Pending Approval'
-- Jalankan di Supabase SQL Editor
-- ============================================================

-- Step 1: Hapus CHECK constraint lama
ALTER TABLE asset_findings DROP CONSTRAINT IF EXISTS asset_findings_status_check;

-- Step 2: UPDATE data lama (jika ada nilai 'Pending' ubah ke 'Pending Approval')
UPDATE asset_findings SET status = 'Pending Approval' WHERE status = 'Pending';

-- Step 3: Buat CHECK constraint baru dengan 'Pending Approval'
ALTER TABLE asset_findings 
ADD CONSTRAINT asset_findings_status_check 
CHECK (status::text = ANY (ARRAY[
    'Open'::text, 
    'On Progress'::text, 
    'Pending Approval'::text, 
    'Done'::text
]));

-- ============================================================
-- VERIFIKASI: Cek constraint setelah dijalankan
-- ============================================================
SELECT 
    con.conname AS constraint_name,
    pg_get_constraintdef(con.oid) AS constraint_definition
FROM pg_constraint con
INNER JOIN pg_class rel ON rel.oid = con.conrelid
WHERE rel.relname = 'asset_findings' AND con.contype = 'c';
