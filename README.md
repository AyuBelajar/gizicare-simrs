# GiziCare — SIMRS Modul Gizi Rawat Inap

## Menjalankan di laptop
1. git clone https://github.com/USERNAME/gizicare.git
2. cd gizicare
3. composer install
4. npm install
5. copy .env.example .env
6. php artisan key:generate
7. Isi DB_PASSWORD di .env (di gc)
8. Terminal 1: php artisan serve
   Terminal 2: npm run dev
9. Buka http://127.0.0.1:8000, login dengan andi@rs.local / password

## Aturan
- Jangan push ke main. Buat branch: fitur/nama-fitur
- Jangan menjalankan migrate:fresh atau db:seed tanpa izin di grup
- Struktur tabel hanya diubah lewat migration baru