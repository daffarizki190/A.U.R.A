<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\AssetFinding;
use App\Models\BeritaAcara;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Role DEV: redirect ke halaman monitoring mereka
        if ($user->role === 'DEV') {
            return redirect()->route('dev.status');
        }
        
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
            // For SPV/PIC or IT - Personal Focus
            
            // 1. ACTIVE REPORTS (Need Attention or In Progress)
            $role_data['active_findings'] = AssetFinding::with('pic')
                ->where('pic_id', $user->id)
                ->whereIn('status', ['Open', 'On Progress'])
                ->latest()
                ->get();
                
            $role_data['active_ba'] = BeritaAcara::where('pic_id', $user->id)
                ->whereIn('status', ['Submitted', 'Processed'])
                ->latest()
                ->get();

            // 2. SUBMITTED / WAITING (Sent to CPM)
            $role_data['waiting_findings'] = AssetFinding::where('pic_id', $user->id)
                ->where('status', 'Pending Approval')
                ->latest()
                ->get();

            // 3. COMPLETED REPORTS (History)
            $role_data['completed_findings'] = AssetFinding::where('pic_id', $user->id)
                ->where('status', 'Done')
                ->latest()
                ->take(5)
                ->get();
                
            $role_data['completed_ba'] = BeritaAcara::where('pic_id', $user->id)
                ->whereIn('status', ['Done', 'Rejected'])
                ->latest()
                ->take(5)
                ->get();
        }

        return view('dashboard.index', compact('stats', 'role_data'));
    }

    public function activityLogs()
    {
        try {
            $logs = ActivityLog::with('user')
                ->latest()
                ->paginate(15);
        } catch (\Exception $e) {
            // Jika tabel belum ada di database, tampilkan paginator kosong
            $logs = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 15);
        }
            
        return view('admin.logs', compact('logs'));
    }
}
