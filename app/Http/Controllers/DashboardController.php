<?php

namespace App\Http\Controllers;

use App\Models\AssetFinding;
use App\Models\BeritaAcara;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
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

        // Custom Role Specific Data
        $role_data = [];
        
        if ($user->role === 'CPM') {
            $role_data['pending_approvals'] = AssetFinding::with('pic')
                ->where('status', 'Pending Approval')
                ->latest()
                ->take(6)
                ->get();
                
            $role_data['recent_activities'] = AssetFinding::with('pic')
                ->latest()
                ->take(4)
                ->get();
        } else {
            // For SPV/PIC or IT
            $role_data['my_active_tasks'] = AssetFinding::with('pic')
                ->where('pic_id', $user->id)
                ->whereIn('status', ['Open', 'On Progress'])
                ->latest()
                ->get();
                
            $role_data['my_submissions'] = AssetFinding::with('pic')
                ->where('pic_id', $user->id)
                ->where('status', 'Pending Approval')
                ->latest()
                ->get();
        }

        return view('dashboard.index', compact('stats', 'role_data'));
    }
}
