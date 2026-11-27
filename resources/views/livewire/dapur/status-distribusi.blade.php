@php
    $warna = [
        'belum'     => 'badge-ghost',
        'disiapkan' => 'badge-warning',
        'dikirim'   => 'badge-info',
        'diterima'  => 'badge-success',
    ];
    $berikutnya = [
        'belum'     => 'Siapkan',
        'disiapkan' => 'Kirim',
        'dikirim'   => 'Diterima',
    ];
@endphp

<div>
    <x-card title="Status distribusi" subtitle="Belum → disiapkan → dikirim → diterima, per bangsal per waktu makan" shadow separator>
        <x-slot:menu>
            <x-datetime wire:model.live="tanggal" class="input-sm" />
        </x-slot:menu>

        <div class="flex flex-wrap items-center justify-between gap-2 mb-3 text-sm">
            <div>Diterima <b>{{ $jumlahDiterima }}</b> dari <b>{{ $totalSel }}</b> pengiriman</div>
            @unless ($bisaMengubah)
                <x-badge value="Mode lihat saja" class="badge-ghost" />
            @endunless
        </div>

        <div class="overflow-x-auto">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Bangsal</th>
                        @foreach ($waktu as $w)
                            <th class="text-center">{{ $w->nama_waktu }} <span class="opacity-60">({{ $w->jam }})</span></th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($bangsal as $b)
                        <tr wire:key="dist-{{ $b->id_bangsal }}">
                            <td class="font-medium">{{ $b->nama_bangsal }}</td>
                            @foreach ($waktu as $w)
                                @php $s = $status[$b->id_bangsal . '-' . $w->id_waktu]->status ?? 'belum'; @endphp
                                <td class="text-center">
                                    <div class="flex flex-col items-center gap-1">
                                        <x-badge :value="ucfirst($s)" class="{{ $warna[$s] }}" />

                                        @if ($bisaMengubah)
                                            <div class="flex gap-1">
                                                @isset($berikutnya[$s])
                                                    <x-button :label="$berikutnya[$s]" icon-right="o-arrow-right"
                                                              wire:click="majukan({{ $b->id_bangsal }}, {{ $w->id_waktu }})"
                                                              class="btn-xs btn-primary" spinner />
                                                @endisset
                                                @if ($s !== 'belum')
                                                    <x-button icon="o-arrow-uturn-left" tooltip="Kembalikan satu langkah"
                                                              wire:click="mundurkan({{ $b->id_bangsal }}, {{ $w->id_waktu }})"
                                                              class="btn-xs btn-ghost" />
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-card>
</div>