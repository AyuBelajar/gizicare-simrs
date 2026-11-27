<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;

#[Title('Kelola Akun & Role')]
class KelolaAkun extends Component
{
    use WithPagination, Toast;

    // ---------- Pencarian & filter ----------
    public string $cari = '';
    public string $filterRole = '';

    // ---------- Form modal tambah / ubah ----------
    public bool $modal = false;
    public ?int $editId = null;
    public string $name = '';
    public string $email = '';
    public string $role = 'ahli_gizi';
    public string $password = '';
    public bool $is_active = true;

    // Keterangan di bawah angka pada kartu ringkasan
    public const KETERANGAN_ROLE = [
        'ahli_gizi'        => 'Skrining, asesmen, order diet',
        'petugas_dapur'    => 'Rekap porsi, etiket, sisa makanan',
        'admin'            => 'Akses penuh',
        'kepala_instalasi' => 'Lihat & ekspor laporan',
    ];

    // Kembali ke halaman 1 setiap kali pencarian / filter berubah
    public function updatedCari(): void
    {
        $this->resetPage();
    }

    public function updatedFilterRole(): void
    {
        $this->resetPage();
    }

    public function pilihRole(string $role): void
    {
        $this->filterRole = $role;
        $this->resetPage();
    }

    // ---------- Buka modal ----------

    public function tambah(): void
    {
        $this->reset(['editId', 'name', 'email', 'password', 'role', 'is_active']);
        $this->resetValidation();
        $this->modal = true;
    }

    public function ubah(int $id): void
    {
        $u = User::findOrFail($id);

        $this->editId    = $u->id_user;
        $this->name      = $u->name;
        $this->email     = $u->email;
        $this->role      = $u->role;
        $this->is_active = $u->is_active;
        $this->password  = '';

        $this->resetValidation();
        $this->modal = true;
    }

    // ---------- Simpan ----------

    public function simpan(): void
    {
        $this->email = strtolower(trim($this->email));

        $data = $this->validate([
            'name'      => 'required|string|max:100',
            'email'     => ['required', 'email', 'max:100', Rule::unique('users', 'email')->ignore($this->editId, 'id_user')],
            'role'      => ['required', Rule::in(array_keys(User::ROLE_LABEL))],
            'is_active' => 'boolean',
            'password'  => [$this->editId ? 'nullable' : 'required', 'min:8'],
        ], [
            'name.required'     => 'Nama wajib diisi.',
            'email.required'    => 'Email wajib diisi.',
            'email.email'       => 'Format email tidak valid.',
            'email.unique'      => 'Email sudah dipakai akun lain.',
            'role.required'     => 'Role wajib dipilih.',
            'password.required' => 'Kata sandi wajib diisi untuk akun baru.',
            'password.min'      => 'Kata sandi minimal 8 karakter.',
        ]);

        $isi = collect($data)->except('password')->all();

        if ($this->password !== '') {
            $isi['password_hash'] = $this->password;   // di-hash otomatis oleh cast di model User
        }

        if ($this->editId) {
            // Admin tidak boleh mengunci dirinya sendiri
            if ($this->editId === auth()->id() && ($isi['role'] !== 'admin' || ! $isi['is_active'])) {
                $this->error('Tidak bisa mengganti role atau menonaktifkan akun sendiri.');
                return;
            }

            User::findOrFail($this->editId)->update($isi);
            $this->success('Akun berhasil diperbarui.');
        } else {
            User::create($isi);
            $this->success('Akun berhasil ditambahkan.');
        }

        $this->modal = false;
    }

    // ---------- Aksi cepat ----------

    public function toggleAktif(int $id): void
    {
        if ($id === auth()->id()) {
            $this->error('Tidak bisa menonaktifkan akun sendiri.');
            return;
        }

        $u = User::findOrFail($id);
        $u->update(['is_active' => ! $u->is_active]);

        $this->success($u->is_active ? "{$u->name} diaktifkan." : "{$u->name} dinonaktifkan.");
    }

    public function hapus(): void
    {
        if (! $this->editId) {
            return;
        }

        if ($this->editId === auth()->id()) {
            $this->error('Tidak bisa menghapus akun sendiri.');
            return;
        }

        $u = User::findOrFail($this->editId);
        $u->delete();   // soft delete: hanya mengisi deleted_at, data tetap ada

        $this->modal = false;
        $this->success("Akun {$u->name} dihapus.");
    }

    // ---------- Format tampilan ----------

    /**
     * "Hari ini 07.12" / "Kemarin 16.20" / "12 Nov 14.30" / "2 Okt 2025"
     */
    public function labelLogin(?CarbonInterface $waktu): string
    {
        if (! $waktu) {
            return 'Belum pernah';
        }

        return match (true) {
            $waktu->isToday()              => 'Hari ini ' . $waktu->format('H.i'),
            $waktu->isYesterday()          => 'Kemarin ' . $waktu->format('H.i'),
            $waktu->year === now()->year   => $waktu->translatedFormat('j M H.i'),
            default                        => $waktu->translatedFormat('j M Y'),
        };
    }

    public function render()
    {
        $users = User::query()
            ->when($this->cari, fn ($q) => $q->where(fn ($q) => $q
                ->where('name', 'ilike', "%{$this->cari}%")
                ->orWhere('email', 'ilike', "%{$this->cari}%")))
            ->when($this->filterRole, fn ($q) => $q->where('role', $this->filterRole))
            ->orderByDesc('is_active')
            ->orderBy('name')
            ->paginate(10);

        $jumlah = User::selectRaw('role, count(*) as total')->groupBy('role')->pluck('total', 'role');
        $aktif  = User::where('is_active', true)->selectRaw('role, count(*) as total')->groupBy('role')->pluck('total', 'role');

        return view('livewire.admin.kelola-akun', [
            'users'    => $users,
            'jumlah'   => $jumlah,
            'aktif'    => $aktif,
            'opsiRole' => collect(User::ROLE_LABEL)
                ->map(fn ($label, $kode) => ['id' => $kode, 'name' => $label])
                ->values()->all(),
            'hakAkses' => [
                ['Skrining, asesmen, order diet',     'Ubah',  '-',    '-',    'Lihat'],
                ['Rekap porsi, etiket, sisa makanan', 'Lihat', 'Ubah', '-',    'Lihat'],
                ['Master data',                       '-',     '-',    'Ubah', '-'],
                ['Kelola akun & audit log',           '-',     '-',    'Ubah', '-'],
                ['Laporan & ekspor Excel',            '-',     '-',    'Ubah', 'Ubah'],
            ],
        ]);
    }
}