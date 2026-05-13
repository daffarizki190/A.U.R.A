@extends('layouts.app')

@section('content')
<header style="margin-bottom: 40px; display: flex; justify-content: space-between; align-items: flex-end;">
    <div>
        <h1 style="font-size: 2.25rem; font-weight: 800; letter-spacing: -0.03em; color: var(--text-main);">
            Kelola Pengguna
        </h1>
        <p style="color: var(--text-dim); font-size: 0.95rem;">
            Atur akses, tambah personil baru, dan kelola kredensial sistem A.U.R.A
        </p>
    </div>
    <div style="text-align: right;">
        <button onclick="toggleAddUserModal()" class="btn-primary">
            <ion-icon name="person-add-outline" style="margin-right: 8px;"></ion-icon> Tambah User Baru
        </button>
    </div>
</header>

@if(session('success'))
    <div class="glass-card" style="padding: 16px 24px; border-left: 4px solid var(--success); color: var(--success); margin-bottom: 24px; font-weight: 600; font-size: 0.9rem; display: flex; align-items: center; gap: 12px;">
        <ion-icon name="checkmark-circle" style="font-size: 1.2rem;"></ion-icon>
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="glass-card" style="padding: 16px 24px; border-left: 4px solid var(--danger); color: var(--danger); margin-bottom: 24px; font-weight: 600; font-size: 0.9rem; display: flex; align-items: center; gap: 12px;">
        <ion-icon name="alert-circle" style="font-size: 1.2rem;"></ion-icon>
        {{ session('error') }}
    </div>
@endif

<div class="glass-card" style="padding: 32px;">
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Nama Lengkap</th>
                    <th>Email / ID</th>
                    <th>Role</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr>
                    <td style="font-weight: 600; color: var(--text-main);">{{ $user->name }}</td>
                    <td style="color: var(--text-dim);">{{ $user->email }}</td>
                    <td>
                        <span class="badge {{ $user->role === 'DEV' ? 'badge-pendingapproval' : ($user->role === 'CPM' ? 'badge-processed' : 'badge-done') }}">
                            {{ $user->role }}
                        </span>
                    </td>
                    <td>
                        <div style="display: flex; gap: 12px;">
                            <button onclick="openChangePasswordModal('{{ $user->id }}', '{{ $user->name }}')" class="btn-primary" style="padding: 6px 12px; font-size: 0.75rem; background: rgba(255,255,255,0.05); color: var(--text-main); border: 1px solid var(--border);">
                                <ion-icon name="key-outline"></ion-icon> Ganti PW
                            </button>
                            
                            @if($user->id !== auth()->id())
                            <form action="{{ route('dev.users.delete', $user->id) }}" method="POST" onsubmit="return confirm('Hapus user {{ $user->name }}?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-primary" style="padding: 6px 12px; font-size: 0.75rem; background: rgba(239, 68, 68, 0.1); color: var(--danger); border: 1px solid rgba(239, 68, 68, 0.2);">
                                    <ion-icon name="trash-outline"></ion-icon>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Tambah User -->
<div id="addUserModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.8); backdrop-filter: blur(8px); z-index: 1000; align-items: center; justify-content: center; padding: 20px;">
    <div class="glass-card" style="width: 100%; max-width: 500px; padding: 40px; border-color: var(--primary);">
        <h2 style="margin-bottom: 24px; font-size: 1.5rem; font-weight: 800; letter-spacing: -0.02em;">Tambah User Baru</h2>
        
        <form action="{{ route('dev.users.store') }}" method="POST">
            @csrf
            <div class="form-group" style="margin-bottom: 20px;">
                <label>Nama Lengkap</label>
                <input type="text" name="name" class="input" required placeholder="Contoh: Rizal Maulana">
            </div>
            <div class="form-group" style="margin-bottom: 20px;">
                <label>Email / ID Login</label>
                <input type="email" name="email" class="input" required placeholder="user@gandariacity.com">
            </div>
            <div class="form-group" style="margin-bottom: 20px;">
                <label>Role / Jabatan</label>
                <select name="role" class="input" required>
                    <option value="SPV">SPV (Supervisor)</option>
                    <option value="CPM">CPM (Manager)</option>
                    <option value="IT">IT (Teknis)</option>
                    <option value="DEV">DEV (Monitor)</option>
                </select>
            </div>
            <div class="form-group" style="margin-bottom: 30px;">
                <label>Password Awal</label>
                <input type="password" name="password" class="input" required placeholder="Minimal 6 karakter">
            </div>
            
            <div style="display: flex; gap: 12px;">
                <button type="submit" class="btn-primary" style="flex: 1;">Simpan User</button>
                <button type="button" onclick="toggleAddUserModal()" class="btn-primary" style="flex: 1; background: transparent; border: 1px solid var(--border); color: var(--text-dim);">Batal</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Ganti Password -->
<div id="changePasswordModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.8); backdrop-filter: blur(8px); z-index: 1000; align-items: center; justify-content: center; padding: 20px;">
    <div class="glass-card" style="width: 100%; max-width: 450px; padding: 40px; border-color: var(--accent);">
        <h2 id="pwModalTitle" style="margin-bottom: 12px; font-size: 1.5rem; font-weight: 800; letter-spacing: -0.02em;">Ganti Password</h2>
        <p style="color: var(--text-dim); font-size: 0.85rem; margin-bottom: 30px;">Masukkan password baru untuk user ini.</p>
        
        <form id="pwForm" action="" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group" style="margin-bottom: 30px;">
                <label>Password Baru</label>
                <input type="password" name="password" class="input" required placeholder="Minimal 6 karakter">
            </div>
            
            <div style="display: flex; gap: 12px;">
                <button type="submit" class="btn-primary" style="flex: 1; background: var(--accent); color: #000;">Update Password</button>
                <button type="button" onclick="closeChangePasswordModal()" class="btn-primary" style="flex: 1; background: transparent; border: 1px solid var(--border); color: var(--text-dim);">Batal</button>
            </div>
        </form>
    </div>
</div>

<script>
    function toggleAddUserModal() {
        const modal = document.getElementById('addUserModal');
        modal.style.display = modal.style.display === 'none' ? 'flex' : 'none';
    }

    function openChangePasswordModal(userId, userName) {
        const modal = document.getElementById('changePasswordModal');
        const form = document.getElementById('pwForm');
        const title = document.getElementById('pwModalTitle');
        
        form.action = `/dev/users/${userId}/password`;
        title.innerText = `Ganti Password: ${userName}`;
        modal.style.display = 'flex';
    }

    function closeChangePasswordModal() {
        document.getElementById('changePasswordModal').style.display = 'none';
    }
</script>
@endsection
