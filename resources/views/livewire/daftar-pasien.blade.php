<div>
    <x-header title="Pasien Rawat Inap" :subtitle="'Pasien · '.$rows->total().' pasien aktif'" separator />

    <x-card shadow class="mb-5">
        <div class="grid gap-3 md:grid-cols-3">
            <x-input wire:model.live.debounce.300ms="search" placeholder="Cari nama, No. RM, atau kamar…" icon="o-magnifying-glass" clearable />
            <x-select wire:model.live="bangsal" :options="$bangsalOptions" placeholder="Semua bangsal" placeholder-value="" />
            <x-select wire:model.live="diet" :options="$dietOptions" placeholder="Semua jenis diet" placeholder-value="" />
        </div>
        <div class="mt-3 flex flex-wrap items-center gap-2">
            <span class="text-sm font-semibold">Status risiko:</span>
            @foreach ($risikoOpsi as $k => $label)
                <button type="button" wire:click="$set('risiko', '{{ $k }}')"
                        class="btn btn-xs {{ $risiko === (string) $k ? 'btn-primary' : 'btn-ghost' }}">{{ $label }}</button>
            @endforeach
        </div>
    </x-card>

    <x-card shadow>
        <div class="overflow-x-auto">
            <table class="table">
                <thead>
                    <tr>
                        <th>Pasien</th><th>No. RM</th><th>Kamar</th><th>Diet aktif</th>
                        <th>Bentuk</th><th>Alergi</th><th>Risiko gizi</th><th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rows as $r)
                        @php
                            $p = $r->pasien;
                            $d = $diet_aktif->get($r->id_rawat);
                            $s = $skrining->get($r->id_rawat);
                        @endphp
                        <tr wire:key="rawat-{{ $r->id_rawat }}">
                            <td>
                                <div class="flex items-center gap-3">
                                    <div class="flex size-10 flex-none items-center justify-center rounded-full bg-primary/20 text-sm font-bold">{{ $p->inisial }}</div>
                                    <div>
                                        <div class="font-semibold">{{ $p->nama_pasien }}</div>
                                        <div class="text-xs text-base-content/60">{{ $p->umur }} th · hari rawat ke-{{ $r->hari_rawat }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="font-mono text-sm">{{ $p->no_rm }}</td>
                            <td>{{ $r->kamar->label }}</td>
                            <td>{{ $d->nama_diet ?? '—' }}</td>
                            <td>{{ $d->nama_bentuk ?? '—' }}</td>
                            <td>
                                @forelse ($p->alergi as $a)
                                    <span class="badge badge-error badge-outline mr-1">{{ $a->alergen->nama_alergen }}</span>
                                @empty
                                    —
                                @endforelse
                            </td>
                            <td>
                                @if ($s)
                                    <span class="badge {{ \App\Support\RingkasanGizi::badgeRisiko($s->status_risiko) }}">{{ ucfirst($s->status_risiko) }}</span>
                                @else
                                    <span class="badge badge-ghost">Belum diskrining</span>
                                @endif
                            </td>
                            <td>
                                <div class="flex justify-end gap-1">
                                    <x-button label="Detail" :link="route('pasien.show', $r->id_rawat)" class="btn-outline btn-xs" />
                                    <x-button label="Skrining" :link="route('pasien.show', ['idRawat' => $r->id_rawat, 'tab' => 'skrining'])" class="btn-primary btn-xs" />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="py-10 text-center text-base-content/60">Tidak ada pasien yang cocok dengan pencarian.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4 flex flex-wrap items-center justify-between gap-3">
            <span class="text-sm text-base-content/60">Menampilkan {{ $rows->count() }} dari {{ $rows->total() }} pasien</span>
            {{ $rows->links() }}
        </div>
    </x-card>
</div>