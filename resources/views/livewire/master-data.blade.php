<div>
    <x-header title="Master Data" subtitle="Pengaturan · data acuan sistem" separator />

    <div class="mb-5 flex flex-wrap gap-2">
        @foreach ($tabs as $key => [$label, $komponen])
            <button type="button" wire:click="$set('tab', '{{ $key }}')"
                    class="btn btn-sm {{ $tab === $key ? 'btn-primary' : 'btn-ghost' }}">{{ $label }}</button>
        @endforeach
    </div>

    <div class="grid gap-6">
        @foreach ($tabs[$tab][1] as $nama)
            @livewire($nama, [], key($tab.'-'.$nama))
        @endforeach
    </div>
</div>