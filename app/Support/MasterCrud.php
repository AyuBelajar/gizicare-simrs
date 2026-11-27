<?php

namespace App\Support;

use Illuminate\Database\QueryException;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;

abstract class MasterCrud extends Component
{
    use WithPagination, Toast;

    public string $search = '';
    public bool $showForm = false;
    public bool $showDelete = false;
    public ?int $editingId = null;
    public ?int $deleteId = null;
    public array $form = [];

    /** Class model, mis. \App\Models\JenisDiet::class */
    abstract protected function modelClass(): string;

    /** Judul kartu, mis. Jenis diet */
    abstract protected function judul(): string;

    /**
     * Daftar kolom form. Kunci yang dikenali:
     * key, label, type (text|textarea|time|number|select), rules, unique, uniqueWith,
     * cari (ikut pencarian), col (kolom di tabel, boleh titik: bangsal.nama_bangsal),
     * tabel (false = tidak tampil di tabel), default.
     */
    abstract protected function fields(): array;

    protected function pageName(): string { return 'page'; }

    protected function relasi(): array { return []; }

    /** Pilihan untuk field bertipe select: [['id' => 1, 'name' => 'Melati'], ...] */
    protected function opsi(string $key): array { return []; }

    protected function urutan(): string
    {
        return (new ($this->modelClass()))->getKeyName();
    }

    public function mount(): void
    {
        $this->kosongkanForm();
    }

    public function updatingSearch(): void
    {
        $this->resetPage($this->pageName());
    }

    protected function kosongkanForm(): void
    {
        $this->form = collect($this->fields())
            ->mapWithKeys(fn ($f) => [$f['key'] => $f['default'] ?? ''])->all();
        $this->editingId = null;
        $this->resetValidation();
    }

    public function buka(): void
    {
        $this->kosongkanForm();
        $this->showForm = true;
    }

    public function ubah(int $id): void
    {
        $row = $this->modelClass()::findOrFail($id);
        $this->kosongkanForm();
        foreach ($this->fields() as $f) {
            $this->form[$f['key']] = $row->{$f['key']};
        }
        $this->editingId = $id;
        $this->showForm = true;
    }

    protected function aturan(): array
    {
        $model = new ($this->modelClass());
        $rules = [];
        $names = [];

        foreach ($this->fields() as $f) {
            $r = explode('|', $f['rules'] ?? 'nullable');
            if (! empty($f['unique'])) {
                $u = Rule::unique($model->getTable(), $f['key'])->ignore($this->editingId, $model->getKeyName());
                if (! empty($f['uniqueWith'])) {
                    $u->where($f['uniqueWith'], $this->form[$f['uniqueWith']] ?? null);
                }
                $r[] = $u;
            }
            $rules['form.'.$f['key']] = $r;
            $names['form.'.$f['key']] = strtolower($f['label']);
        }

        return [$rules, $names];
    }

    public function simpan(): void
    {
        [$rules, $names] = $this->aturan();

        $this->validate($rules, [
            'required' => ':attribute wajib diisi.',
            'unique' => ':attribute sudah dipakai.',
            'max' => ':attribute maksimal :max karakter.',
            'integer' => ':attribute harus berupa angka.',
            'min' => ':attribute minimal :min.',
            'date_format' => ':attribute harus berformat jam:menit.',
        ], $names);

        $data = array_map(fn ($v) => $v === '' ? null : $v, $this->form);
        $model = $this->modelClass();

        if ($this->editingId) {
            $model::findOrFail($this->editingId)->update($data);
            $this->success('Data diperbarui.');
        } else {
            $model::create($data);
            $this->success('Data ditambahkan.');
        }

        $this->showForm = false;
    }

    public function konfirmasiHapus(int $id): void
    {
        $this->deleteId = $id;
        $this->showDelete = true;
    }

    public function hapus(): void
    {
        try {
            $this->modelClass()::findOrFail($this->deleteId)->delete();
            $this->success('Data dihapus.');
        } catch (QueryException $e) {
            $this->error('Data masih dipakai di tabel lain, jadi tidak bisa dihapus.');
        }
        $this->showDelete = false;
    }

    public function render()
    {
        $model = $this->modelClass();
        $fields = $this->fields();

        $query = $model::query()->with($this->relasi());
        if ($this->search !== '') {
            $like = '%'.$this->search.'%';
            $query->where(function ($q) use ($fields, $like) {
                foreach ($fields as $f) {
                    if (! empty($f['cari'])) {
                        $q->orWhere($f['key'], 'ilike', $like); // ilike = tidak peka huruf besar/kecil (khusus PostgreSQL)
                    }
                }
            });
        }

        $headers = collect($fields)
            ->filter(fn ($f) => $f['tabel'] ?? true)
            ->map(fn ($f) => ['key' => $f['col'] ?? $f['key'], 'label' => $f['label']])
            ->values()->all();

        $opsi = collect($fields)->where('type', 'select')
            ->mapWithKeys(fn ($f) => [$f['key'] => $this->opsi($f['key'])])->all();

        return view('livewire.master.crud', [
            'rows' => $query->orderBy($this->urutan())->paginate(8, pageName: $this->pageName()),
            'headers' => $headers,
            'fields' => $fields,
            'judul' => $this->judul(),
            'opsi' => $opsi,
        ]);
    }
}