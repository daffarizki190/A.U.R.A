<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    DB::statement("ALTER TABLE berita_acaras DROP CONSTRAINT berita_acaras_status_check;");
    DB::statement("ALTER TABLE berita_acaras ADD CONSTRAINT berita_acaras_status_check CHECK (status::text = ANY (ARRAY['Pending Approval'::character varying, 'Approved'::character varying, 'Processed'::character varying, 'Done'::character varying, 'Rejected'::character varying, 'Draft'::character varying, 'Submitted'::character varying]::text[]));");
    echo "SUCCESS\n";
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
