<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.guest')]
#[Title('Masuk')]
class Login extends Component
{
    public string $email = '';
    public string $password = '';

    // Role akun yang terdeteksi dari email (untuk menyorot kotak "Masuk sebagai")
    public ?string $roleTerdeteksi = null;

    // Modal "Lupa kata sandi?"
    public bool $modalLupa = false;

    /**
     * Dijalankan otomatis setiap kali isi email berubah.
     * Mencari akun aktif dengan email itu, lalu menyimpan role-nya.
     */
    public function updatedEmail(): void
    {
        $this->roleTerdeteksi = null;
        $this->resetErrorBag('email');

        $email = $this->emailBersih();
        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return;
        }

        $this->roleTerdeteksi = User::where('email', $email)
            ->where('is_active', true)
            ->value('role');
    }

    /**
     * Hapus pesan "kata sandi salah" begitu pengguna mengetik ulang.
     */
    public function updatedPassword(): void
    {
        $this->resetErrorBag('password');
    }

    public function login()
    {
        $this->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ], [
            'email.required'    => 'Email wajib diisi.',
            'email.email'       => 'Format email tidak valid.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        $email = $this->emailBersih();

        // Batasi percobaan: maksimal 5 kali gagal per menit untuk email + IP yang sama
        $kunci = 'login:' . $email . '|' . request()->ip();
        if (RateLimiter::tooManyAttempts($kunci, 5)) {
            $detik = RateLimiter::availableIn($kunci);
            $this->addError('password', "Terlalu banyak percobaan. Coba lagi dalam {$detik} detik.");
            return;
        }

        $user = User::where('email', $email)->first();

        // 1. Email tidak terdaftar
        if (! $user) {
            RateLimiter::hit($kunci, 60);
            $this->addError('email', 'Email tidak terdaftar.');
            return;
        }

        // 2. Akun dinonaktifkan Admin
        if (! $user->is_active) {
            $this->addError('email', 'Akun ini sedang nonaktif. Hubungi Admin.');
            return;
        }

        // 3. Kata sandi salah
        if (! Hash::check($this->password, $user->password_hash)) {
            RateLimiter::hit($kunci, 60);
            $this->addError('password', 'Kata sandi salah.');
            $this->password = '';
            return;
        }

        // Berhasil
        RateLimiter::clear($kunci);

        Auth::login($user);            // memicu event Login → tercatat di audit_log
        session()->regenerate();

        $user->forceFill(['last_login' => now()])->saveQuietly();

        return $this->redirect(self::halamanAwal($user->role));
    }

    private function emailBersih(): string
    {
        return strtolower(trim($this->email));
    }

    /**
     * Halaman pertama setelah login, sesuai role.
     */
    public static function halamanAwal(string $role): string
    {
        return match ($role) {
            'ahli_gizi'        => route('gizi.dashboard'),
            'petugas_dapur'    => route('dapur.rekap'),
            'admin'            => route('admin.akun'),
            'kepala_instalasi' => route('kepala.dashboard'),
            default            => route('login'),
        };
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}