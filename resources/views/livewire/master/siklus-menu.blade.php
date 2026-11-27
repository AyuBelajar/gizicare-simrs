<div>
    <x-card title="Siklus menu" subtitle="Menu per hari, waktu makan, dan jenis diet. Dipakai untuk cek bentrok alergi." shadow separator>
        <x-slot:menu>
            <x-button label="Tambah menu" icon="o-plus" class="btn-primary btn-sm" wire:click="tambah" />
        </x-slot:menu>

        {{-- Filter --}}
        <div class="grid md:grid-cols-2 gap-3 mb-4">
            <x-select label="Hari" wire:model.live="filterHari" :options="$opsiHari" placeholder="Semua hari" />
            <x-select label="Jenis diet" wire:model.live="filterDiet" :options="$opsiDiet" placeholder="Semua jenis diet" />
        </div>

        <x-table :headers="$headers" :rows="$menu" with-pagination>
            @scope('cell_hari_ke', $m)
                <x-badge :value="'Hari ' . $m->hari_ke" class="badge-soft" />
            @endscope

            @scope('cell_deskripsi_menu', $m)
                <span class="whitespace-pre-line">{{ $m->deskripsi_menu }}</span>
            @endscope

            @scope('actions', $m)
                <div class="flex gap-1">
                    <x-button icon="o-pencil" wire:click="ubah({{ $m->id_menu }})" class="btn-ghost btn-sm" tooltip="Ubah" />
                    <x-button icon="o-trash" wire:click="hapus({{ $m->id_menu }})" wire:confirm="Hapus menu ini?"
                              class="btn-ghost btn-sm text-error" tooltip="Hapus" spinner />
                </div>
            @endscope

            <x-slot:empty>
                <div class="py-6 text-center opacity-60">Belum ada menu untuk filter ini.</div>
            </x-slot:empty>
        </x-table>
    </x-card>

    {{-- Modal tambah / ubah --}}
    <x-modal wire:model="modal" :title="$idMenu ? 'Ubah menu' : 'Tambah menu'" separator>
        <x-form wire:submit="simpan">
            <div class="grid md:grid-cols-2 gap-3">
                <x-select label="Hari ke-" wire:model.live="hari_ke" :options="$opsiHari" placeholder="Pilih hari" />
                <x-select label="Waktu makan" wire:model.live="id_waktu" :options="$opsiWaktu" placeholder="Pilih waktu makan" />
            </div>
            <x-select label="Jenis diet" wire:model="id_diet" :options="$opsiDiet" placeholder="Pilih jenis diet" />
            <x-textarea label="Deskripsi menu" wire:model="deskripsi_menu" rows="4"
                        hint="Tulis lengkap, misalnya: Nasi, ayam bakar, tumis buncis, pepaya. Nama bahan dipakai untuk cek alergi." />

            <x-slot:actions>
                <x-button label="Batal" @click="$wire.modal = false" />
                <x-button label="Simpan" type="submit" icon="o-check" class="btn-primary" spinner="simpan" />
            </x-slot:actions>
        </x-form>
    </x-modal>
</div>