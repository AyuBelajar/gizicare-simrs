<div>
    <x-header title="Detail Pasien" :subtitle="'Pasien · '.$r->kamar->label.' · '.$p->nama_pasien" separator>
        <x-slot:actions>
            <x-button label="Kembali" icon="o-arrow-left" :link="route('pasien.index')" class="btn-ghost btn-sm" />
        </x-slot:actions>
    </x-header>

    {{-- Kartu identitas --}}
    <x-card shadow class="mb-5">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="flex size-14 flex-none items-center justify-center rounded-full bg-primary/20 text-lg font-bold">{{ $p->inisial }}</div>
                <div>
                    <h2 class="text-xl font-extrabold">{{ $p->nama_pasien }}</h2>
                    <div class="mt-1 flex flex-wrap gap-2">
                        @if ($skrining)
                            <span class="badge {{ \App\Support\RingkasanGizi::badgeRisiko($skrining->status_risiko) }}">Risiko {{ $skrining->status_risiko }} · {{ $skrining->instrumen }} {{ $skrining->skor }}</span>
                        @else
                            <span class="badge badge-ghost">Belum diskrining</span>
                        @endif
                        @foreach ($p->alergi as $a)
                            <span class="badge badge-error badge-outline">Alergi {{ strtolower($a->alergen->nama_alergen) }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="flex gap-2">
                <x-button label="Skrining ulang" icon="o-clipboard-document-check" wire:click="$set('tab', 'skrining')" class="btn-outline btn-sm" />
                <x-button label="Ubah diet" icon="o-pencil-square" wire:click="$set('tab', 'order-diet')" class="btn-primary btn-sm" />
            </div>
        </div>

        <div class="mt-5 grid gap-4 text-sm sm:grid-cols-2 lg:grid-cols-5">
            <div><div class="text-xs text-base-content/60">No. RM</div><div class="font-semibold">{{ $p->no_rm }}</div></div>
            <div><div class="text-xs text-base-content/60">Usia, jenis kelamin</div><div class="font-semibold">{{ $p->umur }} th, {{ $p->jenis_kelamin === 'L' ? 'laki-laki' : 'perempuan' }}</div></div>
            <div><div class="text-xs text-base-content/60">Kamar</div><div class="font-semibold">{{ $r->kamar->label }}</div></div>
            <div><div class="text-xs text-base-content/60">Masuk</div><div class="font-semibold">{{ $r->tgl_masuk->translatedFormat('d M Y') }} · hari ke-{{ $r->hari_rawat }}</div></div>
            <div><div class="text-xs text-base-content/60">Dx medis</div><div class="font-semibold">{{ $r->diagnosis_medis ?? '—' }}</div></div>
        </div>
    </x-card>

    {{-- Tab --}}
    <div class="mb-5 flex flex-wrap gap-2">
        @foreach ($tabs as $key => $t)
            <button type="button" wire:click="$set('tab', '{{ $key }}')"
                    class="btn btn-sm {{ $tab === $key ? 'btn-primary' : 'btn-ghost' }}">{{ $t[0] }}</button>
        @endforeach
    </div>

    @if ($tab === 'ringkasan')
        <div class="grid gap-5 lg:grid-cols-2">
            <x-card title="Identitas" shadow separator>
                @foreach ([
                    'Nama lengkap' => $p->nama_pasien,
                    'No. RM' => $p->no_rm,
                    'Tanggal lahir' => $p->tgl_lahir->translatedFormat('d M Y').' ('.$p->umur.' th)',
                    'Jenis kelamin' => $p->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan',
                    'Kategori' => ucfirst($p->kategori).($p->kategori === 'anak' ? ' (STRONGkids)' : ' (MST / NRS-2002)'),
                    'No. HP' => $p->no_hp ?? '—',
                ] as $label => $nilai)
                    <div class="flex justify-between gap-4 border-b border-base-200 py-2 text-sm">
                        <span class="text-base-content/60">{{ $label }}</span><span class="font-medium">{{ $nilai }}</span>
                    </div>
                @endforeach
            </x-card>

            <x-card title="Data rawat inap" shadow separator>
                @foreach ([
                    'Bangsal' => $r->kamar->bangsal->nama_bangsal,
                    'Kamar' => $r->kamar->no_kamar.' · Kelas '.$r->kamar->kelas,
                    'Tanggal masuk' => $r->tgl_masuk->translatedFormat('d M Y, H.i'),
                    'Hari rawat' => 'Ke-'.$r->hari_rawat,
                    'Diagnosis medis' => $r->diagnosis_medis ?? '—',
                    'Status' => ucfirst($r->status),
                    'Tanggal keluar' => $r->tgl_keluar?->translatedFormat('d M Y, H.i') ?? '—',
                ] as $label => $nilai)
                    <div class="flex justify-between gap-4 border-b border-base-200 py-2 text-sm">
                        <span class="text-base-content/60">{{ $label }}</span><span class="font-medium">{{ $nilai }}</span>
                    </div>
                @endforeach
            </x-card>

            <x-card title="Alergi makanan" shadow separator>
                <x-slot:menu>
                    <x-button label="Tambah" icon="o-plus" wire:click="$set('showAlergi', true)" class="btn-primary btn-sm" />
                </x-slot:menu>
                @forelse ($p->alergi as $a)
                    <div class="flex items-center justify-between gap-3 border-b border-base-200 py-2 text-sm" wire:key="alergi-{{ $a->id_pa }}">
                        <div>
                            <span class="badge badge-error badge-outline">{{ $a->alergen->nama_alergen }}</span>
                            <span class="ml-2 text-base-content/60">{{ $a->reaksi ?? 'Reaksi tidak dicatat' }}</span>
                        </div>
                        <x-button icon="o-trash" wire:click="hapusAlergi({{ $a->id_pa }})" wire:confirm="Hapus alergi ini?" class="btn-ghost btn-sm text-error" />
                    </div>
                @empty
                    <div class="py-4 text-sm text-base-content/60">Tidak ada alergi yang tercatat.</div>
                @endforelse
            </x-card>

            <x-card title="Ringkasan gizi" shadow separator>
                <div class="flex justify-between gap-4 border-b border-base-200 py-2 text-sm">
                    <span class="text-base-content/60">Diet aktif</span>
                    <span class="font-medium">{{ $dietAktif ? $dietAktif->nama_diet.' · '.($dietAktif->nama_bentuk ?? '—') : 'Belum ada order diet' }}</span>
                </div>
                <div class="flex justify-between gap-4 border-b border-base-200 py-2 text-sm">
                    <span class="text-base-content/60">Catatan diet</span>
                    <span class="font-medium">{{ $dietAktif->catatan ?? '—' }}</span>
                </div>
                <div class="flex justify-between gap-4 py-2 text-sm">
                    <span class="text-base-content/60">Skrining terakhir</span>
                    <span class="font-medium">{{ $skrining ? $skrining->instrumen.' · skor '.$skrining->skor.' · '.$skrining->status_risiko : 'Belum diskrining' }}</span>
                </div>
            </x-card>
        </div>
    @else
        @php [$label, $komponen, $kelas, $pemilik] = $tabs[$tab]; @endphp
        @if (class_exists($kelas))
            @livewire($komponen, ['idRawat' => $idRawat], key($tab.'-'.$idRawat))
        @else
            <x-card shadow>
                <div class="py-10 text-center text-base-content/60">Tab {{ $label }} belum tersedia. Sedang dikerjakan {{ $pemilik }}.</div>
            </x-card>
        @endif
    @endif

    {{-- Modal tambah alergi --}}
    <x-modal wire:model="showAlergi" title="Tambah alergi" persistent>
        <x-form wire:submit="tambahAlergi">
            <x-select label="Alergen" wire:model="alergenId" :options="$alergenOptions" placeholder="Pilih alergen…" />
            <x-input label="Reaksi (opsional)" wire:model="reaksi" placeholder="Contoh: gatal, bengkak bibir" />
            <x-slot:actions>
                <x-button label="Batal" @click="$wire.showAlergi = false" />
                <x-button label="Simpan" icon="o-check" class="btn-primary" type="submit" spinner="tambahAlergi" />
            </x-slot:actions>
        </x-form>
    </x-modal>
</div>