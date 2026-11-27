<div>
    <x-card :title="$judul.' · '.$rows->total().' data'" shadow separator>
        <x-slot:menu>
            <x-input wire:model.live.debounce.300ms="search" placeholder="Cari…" icon="o-magnifying-glass" clearable class="input-sm" />
            <x-button label="Tambah" icon="o-plus" wire:click="buka" class="btn-primary btn-sm" />
        </x-slot:menu>

        <x-table :headers="$headers" :rows="$rows" with-pagination show-empty-text>
            @scope('actions', $row)
                <div class="flex justify-end gap-1">
                    <x-button icon="o-pencil" wire:click="ubah({{ $row->getKey() }})" class="btn-ghost btn-sm" />
                    <x-button icon="o-trash" wire:click="konfirmasiHapus({{ $row->getKey() }})" class="btn-ghost btn-sm text-error" />
                </div>
            @endscope
        </x-table>
    </x-card>

    {{-- Modal tambah / ubah --}}
    <x-modal wire:model="showForm" :title="($editingId ? 'Ubah ' : 'Tambah ').strtolower($judul)" persistent>
        <x-form wire:submit="simpan">
            @foreach ($fields as $f)
                @php $tipe = $f['type'] ?? 'text'; @endphp
                @if ($tipe === 'select')
                    <x-select :label="$f['label']" wire:model="form.{{ $f['key'] }}" :options="$opsi[$f['key']]" placeholder="Pilih…" />
                @elseif ($tipe === 'textarea')
                    <x-textarea :label="$f['label']" wire:model="form.{{ $f['key'] }}" rows="3" />
                @else
                    <x-input :label="$f['label']" wire:model="form.{{ $f['key'] }}" :type="$tipe" />
                @endif
            @endforeach

            <x-slot:actions>
                <x-button label="Batal" @click="$wire.showForm = false" />
                <x-button label="Simpan" icon="o-check" class="btn-primary" type="submit" spinner="simpan" />
            </x-slot:actions>
        </x-form>
    </x-modal>

    {{-- Modal konfirmasi hapus --}}
    <x-modal wire:model="showDelete" title="Hapus data ini?" subtitle="Data yang dihapus tidak bisa dikembalikan.">
        <x-slot:actions>
            <x-button label="Batal" @click="$wire.showDelete = false" />
            <x-button label="Ya, hapus" icon="o-trash" class="btn-error" wire:click="hapus" spinner="hapus" />
        </x-slot:actions>
    </x-modal>
</div>