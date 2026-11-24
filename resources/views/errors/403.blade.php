<x-layouts.guest title="Akses ditolak">
    <div class="min-h-screen flex items-center justify-center p-6">
        <x-card class="max-w-md text-center" title="Akses ditolak" shadow>
            <p class="mb-6">
                {{ $exception->getMessage() ?: 'Anda tidak punya akses ke halaman ini.' }}
            </p>
            <x-button label="Kembali ke beranda" link="/" icon="o-home" class="btn-primary" />
        </x-card>
    </div>
</x-layouts.guest>