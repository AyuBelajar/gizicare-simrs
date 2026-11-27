<div>
    <x-header title="Rekap Porsi" subtitle="Produksi dapur per waktu makan" separator />

    <div class="grid gap-6">
        <x-card title="Rekap porsi per waktu makan" shadow separator>
            <div class="py-6 text-center opacity-60">
                Rekap porsi akan tampil di sini setelah data order diet tersedia.
            </div>
        </x-card>

        <livewire:dapur.status-distribusi />
    </div>
</div>