<?php

namespace App\Http\Controllers;

use App\Models\AssetFinding;
use App\Models\BeritaAcara;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            // Asset Findings
            'findings_pending'  => AssetFinding::where('status', 'Pending Approval')->count(),
            'findings_open'     => AssetFinding::where('status', 'Open')->count(),
            'findings_progress' => AssetFinding::where('status', 'On Progress')->count(),
            'findings_done'     => AssetFinding::where('status', 'Done')->count(),

            // Berita Acara
            'ba_submitted'      => BeritaAcara::where('status', 'Submitted')->count(),
            'ba_processed'      => BeritaAcara::where('status', 'Processed')->count(),
            'ba_done'           => BeritaAcara::where('status', 'Done')->count(),
            'ba_rejected'       => BeritaAcara::where('status', 'Rejected')->count(),

            // Summary
            'findings_total'    => AssetFinding::count(),
            'ba_total'          => BeritaAcara::count(),
        ];

        return view('dashboard.index', compact('stats'));
    }
}
