<?php

namespace App\Http\Controllers;

use App\Models\AssetFinding;
use App\Models\User;
use Illuminate\Http\Request;

class AssetFindingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = AssetFinding::with('pic');

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->location) {
            $query->where('location', 'like', '%' . $request->location . '%');
        }

        $findings = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        return view('findings.index', compact('findings'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = User::all();
        return view('findings.create', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'location' => 'required',
            'asset_type' => 'required',
            'description' => 'required',
            'finding_date' => 'required|date',
            'photo' => 'nullable|image|max:5120',
        ]);

        $code = 'T-' . date('Ymd') . '-' . str_pad(AssetFinding::count() + 1, 3, '0', STR_PAD_LEFT);

        $data = $request->all();
        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('findings', 's3');
        }

        AssetFinding::create(array_merge($data, [
            'finding_code' => $code,
            'reporter' => auth()->user()->name,
            'pic_id' => auth()->id(), // PIC otomatis adalah pembuat
            'status' => 'Pending Approval', // Status awal
        ]));

        return redirect()->route('findings.index')->with('success', 'Temuan berhasil ditambahkan dan menunggu persetujuan CPM.');
    }

    /**
     * Display the specified resource.
     */
    public function show(AssetFinding $finding)
    {
        return view('findings.show', compact('finding'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AssetFinding $finding)
    {
        $user = auth()->user();
        
        // Aturan Edit:
        // 1. Jika Pending Approval: Pembuat (pic_id) ATAU CPM boleh edit.
        // 2. Jika SUDAH Approved/Open: Hanya CPM yang boleh edit.
        if ($finding->status == 'Pending Approval') {
            if ($user->id !== $finding->pic_id && $user->role !== 'CPM') {
                return redirect()->route('findings.show', $finding)->with('error', 'Hanya pembuat laporan atau CPM yang dapat mengedit saat status Pending.');
            }
        } else {
            if ($user->role !== 'CPM') {
                return redirect()->route('findings.show', $finding)->with('error', 'Hanya CPM yang dapat mengedit laporan yang sudah disetujui.');
            }
        }

        $users = User::all();
        return view('findings.edit', compact('finding', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AssetFinding $finding)
    {
        $user = auth()->user();

        // Validasi akses yang sama dengan edit()
        if ($finding->status == 'Pending Approval') {
            if ($user->id !== $finding->pic_id && $user->role !== 'CPM') {
                return abort(403);
            }
        } else {
            if ($user->role !== 'CPM') {
                return abort(403);
            }
        }

        $data = $request->all();
        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('findings', 's3');
        }

        $finding->update($data);
        return redirect()->route('findings.show', $finding)->with('success', 'Temuan berhasil diupdate.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AssetFinding $finding)
    {
        if (auth()->user()->role !== 'CPM') {
            return redirect()->back()->with('error', 'Hanya CPM yang memiliki wewenang untuk menghapus data.');
        }

        $finding->delete();
        return redirect()->route('findings.index')->with('success', 'Temuan berhasil dihapus.');
    }

    /**
     * Approve the finding (CPM Only).
     */
    public function approve(AssetFinding $finding)
    {
        if (auth()->user()->role !== 'CPM') {
            return redirect()->back()->with('error', 'Hanya CPM yang dapat menyetujui temuan.');
        }

        $finding->update(['status' => 'Open']);

        return redirect()->route('findings.show', $finding)->with('success', 'Temuan telah disetujui.');
    }

    /**
     * Cancel the approval (CPM Only).
     */
    public function cancelApprove(AssetFinding $finding)
    {
        if (auth()->user()->role !== 'CPM') {
            return redirect()->back()->with('error', 'Hanya CPM yang dapat membatalkan persetujuan.');
        }

        $finding->update(['status' => 'Pending Approval']);

        return redirect()->route('findings.show', $finding)->with('success', 'Persetujuan dibatalkan. Status kembali ke Pending Approval.');
    }

    /**
     * Update the status of the finding.
     */
    public function updateStatus(Request $request, AssetFinding $finding)
    {
        $request->validate([
            'status' => 'required|string',
        ]);

        $user = auth()->user();

        if ($user->role !== 'CPM' && $user->id !== $finding->pic_id) {
            return redirect()->back()->with('error', 'Hanya CPM atau PIC (Penanggung Jawab) yang dapat mengubah status.');
        }

        $finding->update(['status' => $request->status]);

        return redirect()->route('findings.show', $finding)->with('success', 'Status temuan berhasil diperbarui.');
    }
}
