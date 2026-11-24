<div class="min-h-screen flex flex-wrap bg-base-200">

    {{-- ============ KIRI: identitas aplikasi ============ --}}
    <section class="hidden lg:flex flex-1 basis-[480px] flex-col justify-between gap-10 p-14 bg-primary text-primary-content">

        {{-- Logo --}}
        <div class="flex items-center gap-3">
            <span class="w-11 h-11 rounded-2xl bg-base-100 flex items-center justify-center">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                     stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M5 19c0-8 5-14 15-14 0 10-6 15-14 15"></path>
                    <path d="M5 19l7-7"></path>
                </svg>
            </span>
            <span class="text-xl font-extrabold">GiziCare</span>
        </div>

        {{-- Judul, deskripsi, kartu fitur --}}
        <div class="flex flex-col gap-5 max-w-[520px]">
            <h1 class="text-[44px] font-extrabold leading-[1.1] tracking-tight">
                Asuhan gizi rawat inap, dari skrining sampai baki makan.
            </h1>
            <p class="text-base leading-relaxed opacity-80">
                Modul Gizi Rawat Inap SIMRS untuk Ahli Gizi, Petugas Dapur, Admin, dan Kepala Instalasi Gizi.
            </p>

            <div class="grid grid-cols-2 gap-3">
                @foreach ([
                    ['Skrining NRS-2002 & STRONGkids', 'o-clipboard-document-list', 'bg-accent text-accent-content'],
                    ['Order diet & riwayat',           'o-queue-list',              'bg-warning text-warning-content'],
                    ['Rekap porsi dapur otomatis',     'o-table-cells',             'bg-base-200 text-base-content'],
                    ['Monitoring sisa makanan',        'o-chart-pie',               'bg-secondary text-secondary-content'],
                ] as [$label, $ikon, $warna])
                    <div class="rounded-2xl p-3.5 flex items-center gap-2.5 text-sm font-semibold {{ $warna }}">
                        <x-icon :name="$ikon" class="w-5 h-5 shrink-0" />
                        <span>{{ $label }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <span class="text-[13px] opacity-80">SIMRS C · Kelompok 2 · 2026</span>
    </section>

    {{-- ============ KANAN: form login ============ --}}
    <section class="flex-1 basis-[420px] flex items-center justify-center px-6 py-12">
        <div class="w-full max-w-[420px]">

            {{-- Nama aplikasi untuk layar kecil --}}
            <div class="lg:hidden mb-6 text-center">
                <div class="text-2xl font-extrabold">GiziCare</div>
                <div class="text-sm opacity-60">SIMRS · Gizi Rawat Inap</div>
            </div>

            <div class="bg-base-100 border border-base-300 rounded-box p-9">
                <form wire:submit="login" class="flex flex-col gap-[18px]">

                    <div class="flex flex-col gap-1.5">
                        <h2 class="text-[26px] font-extrabold tracking-tight">Masuk</h2>
                        <span class="text-sm opacity-70">Gunakan akun petugas yang diberikan admin.</span>
                    </div>

                    {{-- Email: role dicari otomatis 0,5 detik setelah berhenti mengetik --}}
                    <x-input label="Email" wire:model.live.debounce.500ms="email" type="email"
                             placeholder="nama@rs.local" icon="o-envelope" autofocus />

                    <x-password label="Kata sandi" wire:model="password" right />

                    {{-- Masuk sebagai: tersorot otomatis sesuai role akun --}}
                    <fieldset class="flex flex-col gap-2">
                        <legend class="text-sm font-semibold mb-2">Masuk sebagai</legend>

                        <div class="grid grid-cols-2 gap-2">
                            @foreach (\App\Models\User::ROLE_LABEL as $kode => $label)
                                <span @class([
                                    'min-h-11 rounded-xl flex items-center justify-center gap-1.5 text-[13px] transition',
                                    'bg-primary text-primary-content font-bold'       => $roleTerdeteksi === $kode,
                                    'border border-base-300 font-semibold opacity-70' => $roleTerdeteksi !== $kode,
                                ])>
                                    @if ($roleTerdeteksi === $kode)
                                        <x-icon name="o-check-circle" class="w-4 h-4" />
                                    @endif
                                    {{ $label }}
                                </span>
                            @endforeach
                        </div>

                        <span class="text-xs opacity-70">
                            @if ($roleTerdeteksi)
                                Akun ini terdaftar sebagai <b>{{ \App\Models\User::ROLE_LABEL[$roleTerdeteksi] }}</b>.
                            @else
                                Role diambil dari akun; tampilan ini hanya penanda menu yang akan muncul.
                            @endif
                        </span>
                    </fieldset>

                    <x-button label="Masuk" type="submit" class="btn-neutral w-full min-h-12 text-[15px]" spinner="login" />

                    <button type="button" wire:click="$set('modalLupa', true)"
                            class="self-center text-[13px] font-semibold link link-hover">
                        Lupa kata sandi?
                    </button>
                </form>
            </div>
        </div>
    </section>

    {{-- ============ Modal lupa kata sandi ============ --}}
    <x-modal wire:model="modalLupa" title="Lupa kata sandi?" separator>
        <p class="mb-3">
            Kata sandi akun petugas diatur oleh <b>Admin</b> SIMRS. Hubungi Admin atau Instalasi Gizi
            untuk meminta kata sandi baru.
        </p>
        <p class="text-sm opacity-70">
            Admin dapat mengganti kata sandi melalui menu <b>Kelola Akun &amp; Role</b>.
        </p>
        <x-slot:actions>
            <x-button label="Mengerti" class="btn-primary" @click="$wire.modalLupa = false" />
        </x-slot:actions>
    </x-modal>
</div>