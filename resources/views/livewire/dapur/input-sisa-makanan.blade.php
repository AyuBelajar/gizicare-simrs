<div>
    <x-header title="Input Sisa Makanan" subtitle="Metode Comstock: persen makanan yang tersisa di baki" separator />

    {{-- Filter --}}
    <x-card shadow class="mb-4">
        <div class="grid md:grid-cols-3 gap-3">
            <x-datetime label="Tanggal" wire:model.live="tanggal" />
            <x-select label="Bangsal" wire:model.live="id_bangsal" :options="$opsiBangsal" />
            <x-select label="Waktu makan" wire:model.live="id_waktu" :options="$opsiWaktu" />
        </div>
    </x-card>

    <x-card shadow>
        {{-- Ringkasan + aksi --}}
        <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
            <div class="text-sm">
                Terisi <b>{{ $jumlahTerisi }}</b> dari <b>{{ $pasien->count() }}</b> pasien
                @if ($adaPerubahan)
                    <x-badge value="Ada perubahan belum disimpan" class="badge-warning badge-sm ml-2" />
                @endif
            </div>

            @if ($bisaMengisi)
                <div class="flex gap-2">
                    <x-button label="Isi kosong = 0% (habis)" wire:click="isiYangKosong(0)" class="btn-sm" />
                    <x-button label="Simpan" icon="o-check" wire:click="simpan" class="btn-primary btn-sm" spinner="simpan" />
                </div>
            @else
                <x-badge value="Mode lihat saja" class="badge-ghost" />
            @endif
        </div>

        <div class="text-xs opacity-60 mb-3">
            0% = habis · 25% = sisa ¼ · 50% = sisa ½ · 75% = sisa ¾ · 100% = utuh / tidak dimakan
        </div>

        @if ($pasien->isEmpty())
            <div class="py-8 text-center opacity-60">Tidak ada pasien rawat inap aktif di bangsal ini.</div>
        @else
            <div class="overflow-x-auto">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th class="w-10">No</th>
                            <th>Pasien</th>
                            <th>Kamar</th>
                            <th>Skor sisa</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pasien as $i => $r)
                            @php
                                $nilai = $skor[$r->id_rawat] ?? null;
                                $belumDisimpan = $nilai !== ($tersimpan[$r->id_rawat] ?? null);
                            @endphp
                            <tr wire:key="sisa-{{ $r->id_rawat }}" @class(['bg-warning/10' => $belumDisimpan])>
                                <td>{{ $i + 1 }}</td>
                                <td>
                                    <div class="font-medium">{{ $r->pasien->nama_pasien }}</div>
                                    <div class="text-xs opacity-60">RM {{ $r->pasien->no_rm }}</div>
                                </td>
                                <td>{{ $r->kamar->no_kamar }}</td>
                                <td>
                                    <div class="join">
                                        @foreach (\App\Models\SisaMakanan::SKOR as $s)
                                            <button type="button"
                                                    wire:click="pilih({{ $r->id_rawat }}, {{ $s }})"
                                                    @disabled(! $bisaMengisi)
                                                    @class([
                                                        'btn btn-sm join-item',
                                                        'btn-primary' => $nilai === $s,
                                                        'btn-outline' => $nilai !== $s,
                                                        'btn-error'   => $nilai === $s && $s > 50,
                                                    ])>
                                                {{ $s }}%
                                            </button>
                                        @endforeach
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </x-card>
</div>