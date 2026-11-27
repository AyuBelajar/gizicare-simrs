<div class="p-2 lg:p-4 flex flex-col gap-6">

    {{-- ============ HEADER ============ --}}
    <header class="flex flex-col md:flex-row md:items-end md:justify-between gap-4">
        <div>
            <span class="text-sm opacity-60">Pengaturan · role disimpan di kolom users.role</span>
            <h1 class="text-3xl font-extrabold tracking-tight">Kelola Akun &amp; Role</h1>
        </div>

        <div class="flex items-center gap-3">
            <x-input placeholder="Cari nama atau email…" icon="o-magnifying-glass"
                     wire:model.live.debounce.300ms="cari" clearable class="md:w-72" />
            <span class="hidden md:inline text-sm opacity-60 whitespace-nowrap">
                {{ now()->translatedFormat('D, j M Y') }}
            </span>
        </div>
    </header>

    {{-- ============ RINGKASAN PER ROLE ============ --}}
    <section aria-label="Ringkasan" class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach (\App\Models\User::ROLE_LABEL as $kode => $label)
            <button type="button" wire:click="pilihRole('{{ $filterRole === $kode ? '' : $kode }}')"
                    @class([
                        'text-left rounded-box p-5 border transition hover:shadow-sm',
                        'bg-primary border-primary text-primary-content' => $filterRole === $kode,
                        'bg-base-100 border-base-300'                    => $filterRole !== $kode,
                    ])>
                <div class="text-sm font-semibold opacity-80">{{ $label }}</div>
                <div class="text-4xl font-extrabold mt-2">{{ $jumlah[$kode] ?? 0 }}</div>
                <div class="text-xs mt-1 opacity-70">
                    {{ $aktif[$kode] ?? 0 }} aktif · {{ \App\Livewire\Admin\KelolaAkun::KETERANGAN_ROLE[$kode] }}
                </div>
            </button>
        @endforeach
    </section>

    {{-- ============ DAFTAR AKUN ============ --}}
    <section class="bg-base-100 border border-base-300 rounded-box p-5">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4">
            <h2 class="text-lg font-bold">
                Daftar akun · {{ $users->total() }} pengguna
                @if ($filterRole)
                    <span class="badge badge-soft badge-secondary ml-1">{{ \App\Models\User::ROLE_LABEL[$filterRole] }}</span>
                @endif
            </h2>

            <div class="flex gap-2">
                <x-dropdown>
                    <x-slot:trigger>
                        <x-button label="Filter role" icon="o-funnel" class="btn-outline border-base-300" />
                    </x-slot:trigger>
                    <x-menu-item title="Semua role" wire:click="pilihRole('')" />
                    @foreach (\App\Models\User::ROLE_LABEL as $kode => $label)
                        <x-menu-item :title="$label" wire:click="pilihRole('{{ $kode }}')" />
                    @endforeach
                </x-dropdown>

                <x-button label="Tambah akun" icon="o-plus" class="btn-neutral" wire:click="tambah" />
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="table">
                <thead>
                    <tr>
                        <th>Pengguna</th>
                        <th>Role</th>
                        <th>Login terakhir</th>
                        <th>Status</th>
                        <th class="w-12"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $u)
                        <tr wire:key="akun-{{ $u->id_user }}" @class(['opacity-60' => ! $u->is_active])>
                            <td>
                                <div class="font-bold">
                                    {{ $u->name }}
                                    @if ($u->id_user === auth()->id())
                                        <span class="badge badge-sm badge-ghost ml-1">Anda</span>
                                    @endif
                                </div>
                                <div class="text-xs opacity-60">{{ $u->email }}</div>
                            </td>
                            <td>
                                <span class="badge badge-soft badge-secondary">{{ $u->role_label }}</span>
                            </td>
                            <td class="whitespace-nowrap text-sm">
                                {{ $this->labelLogin($u->last_login) }}
                            </td>
                            <td>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" class="toggle toggle-sm toggle-success"
                                           @checked($u->is_active)
                                           @disabled($u->id_user === auth()->id())
                                           wire:click="toggleAktif({{ $u->id_user }})" />
                                    <span class="text-sm">{{ $u->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                                </label>
                            </td>
                            <td>
                                <x-button icon="o-pencil-square" wire:click="ubah({{ $u->id_user }})"
                                          class="btn-ghost btn-sm" tooltip-left="Ubah {{ $u->name }}" />
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-10 opacity-60">
                                Tidak ada akun yang cocok dengan pencarian atau filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $users->links() }}
        </div>
    </section>

    {{-- ============ HAK AKSES PER ROLE ============ --}}
    <section class="bg-base-100 border border-base-300 rounded-box p-5">
        <h2 class="text-lg font-bold mb-4">Hak akses per role (dicek middleware)</h2>

        <div class="overflow-x-auto">
            <table class="table">
                <thead>
                    <tr>
                        <th>Fitur</th>
                        <th>Ahli Gizi</th>
                        <th>Petugas Dapur</th>
                        <th>Admin</th>
                        <th>Kepala</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($hakAkses as $baris)
                        <tr>
                            <td class="font-bold">{{ $baris[0] }}</td>
                            @foreach (array_slice($baris, 1) as $akses)
                                <td>
                                    @if ($akses === 'Ubah')
                                        <span class="badge badge-soft badge-success">✓ Ubah</span>
                                    @elseif ($akses === 'Lihat')
                                        <span class="badge badge-soft badge-accent">Lihat</span>
                                    @else
                                        <span class="opacity-40">–</span>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>

    {{-- ============ MODAL TAMBAH / UBAH ============ --}}
    <x-modal wire:model="modal" :title="$editId ? 'Ubah akun' : 'Tambah akun'" separator>
        <x-form wire:submit="simpan">
            <x-input label="Nama lengkap" wire:model="name" placeholder="mis. Dewi Lestari, S.Gz" />
            <x-input label="Email" wire:model="email" type="email" placeholder="nama@rs.local" />
            <x-select label="Role" wire:model="role" :options="$opsiRole" />
            <x-password label="Kata sandi" wire:model="password" right
                        :hint="$editId ? 'Kosongkan jika tidak diubah. Isi untuk mereset kata sandi petugas.' : 'Minimal 8 karakter.'" />
            <x-toggle label="Akun aktif" wire:model="is_active" class="toggle-success" />

            <x-slot:actions>
                <div class="flex w-full justify-between gap-2">
                    <div>
                        @if ($editId && $editId !== auth()->id())
                            <x-button label="Hapus akun" icon="o-trash" class="btn-ghost text-error"
                                      wire:click="hapus" wire:confirm="Yakin hapus akun ini? Akun tidak bisa login lagi." />
                        @endif
                    </div>
                    <div class="flex gap-2">
                        <x-button label="Batal" @click="$wire.modal = false" />
                        <x-button label="Simpan" type="submit" class="btn-neutral" spinner="simpan" />
                    </div>
                </div>
            </x-slot:actions>
        </x-form>
    </x-modal>
</div>