<?php

namespace App\Http\Controllers;

use App\Models\BeritaAcara;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class BeritaAcaraController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = BeritaAcara::with('pic');

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->ba_type) {
            $query->where('ba_type', 'like', '%' . $request->ba_type . '%');
        }

        $bas = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        return view('ba.index', compact('bas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = User::all();
        return view('ba.create', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'ba_type' => 'required',
            'incident_date' => 'required|date',
            'customer_name' => 'required',
            'chronology' => 'required',
            'attachment' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,png,jpg,jpeg|max:10240',
        ]);

        $code = 'BA/' . date('Y/m/') . str_pad(BeritaAcara::count() + 1, 3, '0', STR_PAD_LEFT);
        $data = $request->except('attachment');

        if ($request->hasFile('attachment')) {
            $data['attachment'] = $request->file('attachment')->store('ba_attachments', 'public');
        }

        BeritaAcara::create(array_merge($data, [
            'ba_number'    => $code,
            'pic_id'       => auth()->id(), // PIC otomatis adalah pembuat
            'status'       => 'Submitted',  // Status awal
            'submitted_at' => Carbon::now(), // ✅ Catat waktu submit
        ]));

        return redirect()->route('ba.index')->with('success', 'BA berhasil dibuat dan menunggu persetujuan CPM.');
    }

    /**
     * Display the specified resource.
     */
    public function show(BeritaAcara $ba)
    {
        return view('ba.show', compact('ba'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BeritaAcara $ba)
    {
        $user = auth()->user();
        
        if ($ba->status == 'Submitted') {
            if ($user->id !== $ba->pic_id && $user->role !== 'CPM') {
                return redirect()->route('ba.show', $ba)->with('error', 'Hanya pembuat laporan atau CPM yang dapat mengedit saat status Pending.');
            }
        } else {
            if ($user->role !== 'CPM') {
                return redirect()->route('ba.show', $ba)->with('error', 'Hanya CPM yang dapat mengedit laporan yang sudah disetujui.');
            }
        }

        $users = User::all();
        return view('ba.edit', compact('ba', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BeritaAcara $ba)
    {
        $user = auth()->user();

        if ($ba->status == 'Submitted') {
            if ($user->id !== $ba->pic_id && $user->role !== 'CPM') {
                return abort(403);
            }
        } else {
            if ($user->role !== 'CPM') {
                return abort(403);
            }
        }
        
        $request->validate([
            'attachment' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,png,jpg,jpeg|max:10240',
        ]);

        $data = $request->except('attachment');

        if ($request->hasFile('attachment')) {
            $data['attachment'] = $request->file('attachment')->store('ba_attachments', 'public');
        }

        $ba->update($data);
        return redirect()->route('ba.show', $ba)->with('success', 'BA berhasil diupdate.');
    }

    /**
     * Print the BA to PDF/Document.
     */
    public function print(BeritaAcara $ba)
    {
        return view('ba.print', compact('ba'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BeritaAcara $ba)
    {
        if (auth()->user()->role !== 'CPM') {
            return redirect()->back()->with('error', 'Hanya CPM yang memiliki wewenang untuk menghapus data.');
        }

        $ba->delete();
        return redirect()->route('ba.index')->with('success', 'BA berhasil dihapus.');
    }

    /**
     * Approve the BA (CPM Only).
     */
    public function approve(BeritaAcara $ba)
    {
        if (auth()->user()->role !== 'CPM') {
            return redirect()->back()->with('error', 'Hanya CPM yang dapat menyetujui BA.');
        }

        $ba->update([
            'status'      => 'Processed',
            'approved_at' => Carbon::now(), // ✅ Catat waktu approval CPM
        ]);

        return redirect()->route('ba.show', $ba)->with('success', 'BA telah disetujui oleh CPM.');
    }

    /**
     * Cancel the approval (CPM Only).
     */
    public function cancelApprove(BeritaAcara $ba)
    {
        if (auth()->user()->role !== 'CPM') {
            return redirect()->back()->with('error', 'Hanya CPM yang dapat membatalkan persetujuan.');
        }

        $ba->update([
            'status'      => 'Submitted',
            'approved_at' => null, // ✅ Reset approved_at saat approval dibatalkan
        ]);

        return redirect()->route('ba.show', $ba)->with('success', 'Persetujuan dibatalkan. Status kembali ke Submitted.');
    }
}
