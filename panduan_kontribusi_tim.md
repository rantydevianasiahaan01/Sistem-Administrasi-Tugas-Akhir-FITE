# Panduan Kontribusi (Contributing Guidelines)

Selamat datang dan terima kasih telah berkontribusi pada proyek ini! Dokumen ini menjelaskan alur kerja, konvensi penamaan branch, standar pesan commit, aturan kualitas kode, serta prosedur Pull Request dan pelaporan issue agar seluruh anggota tim dapat berkolaborasi secara rapi, konsisten, dan terstruktur.

---

## 1. Persiapan Lingkungan Lokal (Local Setup)

1. **Clone repository** ke komputer lokal:
   ```bash
   git clone <URL_REPOSITORY>
   cd <NAMA_FOLDER_PROYEK>
   ```
2. **Salin file environment** (sesuaikan dengan struktur folder proyek, baik di root maupun di subfolder seperti `backend/` atau `frontend/`):
   ```bash
   cp .env.example .env
   ```
3. **Install dependensi** sesuai teknologi yang digunakan pada proyek:
   ```bash
   # Backend (PHP / Laravel)
   composer install
   php artisan key:generate
   php artisan migrate --seed

   # Frontend / Node.js
   npm install
   ```
4. **Jalankan aplikasi secara lokal** dan pastikan tidak ada error sebelum mulai mengembangkan fitur:
   ```bash
   # Server Backend (Laravel)
   php artisan serve

   # Server Frontend / Vite
   npm run dev
   ```

---

## 2. Alur Kerja & Penamaan Branch

**Dilarang melakukan `git push` langsung ke branch `main` atau `develop`.** Setiap perubahan wajib dibuat pada branch terpisah dan diajukan melalui Pull Request (PR).

### Branch Utama
* **`main`**: Branch produksi yang stabil dan siap rilis. Hanya menerima merge dari `develop` atau `hotfix/*`.
* **`develop`**: Branch integrasi utama tempat fitur dan perbaikan digabungkan serta diuji sebelum rilis.

### Format Penamaan Branch Kerja
Buat branch baru dari `develop` menggunakan format `<tipe>/<deskripsi-singkat-kebab-case>`:

| Prefix Branch | Kegunaan | Contoh Nama Branch |
| :--- | :--- | :--- |
| `feat/` | Penambahan fitur atau modul baru | `feat/manajemen-inventori` |
| `fix/` | Perbaikan bug pada kode | `fix/validasi-form-login` |
| `hotfix/` | Perbaikan bug kritis langsung dari `main` | `hotfix/error-koneksi-db` |
| `refactor/` | Perbaikan struktur kode tanpa mengubah perilaku | `refactor/service-pembayaran` |
| `style/` | Perubahan tampilan antarmuka (UI/CSS) atau format | `style/responsif-halaman-profil` |
| `test/` | Penambahan atau pembaruan unit/feature test | `test/auth-controller-test` |
| `docs/` | Perubahan pada dokumentasi (`README`, `CONTRIBUTING`, dll.) | `docs/panduan-setup-lokal` |
| `chore/` | Pembaruan dependensi, konfigurasi, atau CI/CD | `chore/konfigurasi-ci` |

### Langkah Membuat Branch Baru
```bash
git checkout develop
git pull origin develop
git checkout -b feat/nama-fitur-kamu
```

---

## 3. Konvensi Pesan Commit

Gunakan format **Conventional Commits** agar riwayat perubahan jelas dan mudah ditelusuri:

```text
<tipe>(<scope-opsional>): <deskripsi singkat dalam huruf kecil>
```

### Contoh Pesan Commit
* `feat(auth): tambahkan fitur login dan logout pengguna`
* `fix(order): perbaiki perhitungan subtotal pada keranjang`
* `refactor(api): pisahkan validasi input ke form request`
* `style(layout): perbaiki jarak elemen pada tampilan mobile`
* `test(produk): tambahkan pengujian endpoint daftar produk`
* `docs: perbarui langkah instalasi pada dokumentasi`
* `chore: tambahkan konfigurasi linter proyek`

### Aturan Penulisan Commit
* Gunakan kata kerja aktif, jelas, dan singkat (maksimal 72 karakter pada baris pertama).
* Jaga setiap commit tetap fokus pada satu perubahan kecil; jangan mencampur beberapa fitur atau perbaikan yang tidak berkaitan dalam satu commit besar.
* Hindari pesan commit yang ambigu seperti `update`, `fix`, `wip`, `revisi`, atau `edit`.

---

## 4. Standar Kualitas Kode

Sebelum melakukan commit maupun push, pastikan seluruh poin berikut telah diperiksa:

1. **Linter & Formatter:** Kode telah diformat dan lolos pengecekan linter proyek (contoh: `./vendor/bin/pint` untuk Laravel/PHP atau `npm run lint` untuk JS/TS).
2. **Bersih dari Kode Debugging:** Hapus seluruh sisa `dd()`, `dump()`, `var_dump()`, `print_r()`, atau `console.log()`.
3. **Keamanan Data & Rahasia:** **Jangan pernah** meng-commit file `.env`, API key, token akses, password, atau kredensial rahasia ke repository.
4. **Migrasi Database:** Jika mengubah struktur database, buat file migration baru alih-alih mengubah file migration lama yang sudah di-merge ke `develop`.
5. **Pengujian:** Pastikan perubahan berjalan sesuai ekspektasi dan tidak merusak fitur yang sudah ada (jalankan `php artisan test` atau `npm test` jika tersedia).

---

## 5. Prosedur Pull Request (PR)

1. **Sinkronkan branch dengan `develop`:**
   ```bash
   git fetch origin
   git merge origin/develop
   ```
   Selesaikan *merge conflict* (jika ada) di lokal sebelum melanjutkan.
2. **Push branch ke repository remote:**
   ```bash
   git push -u origin feat/nama-fitur-kamu
   ```
3. **Buat Pull Request:**
   * Arahkan **base branch** ke **`develop`** (kecuali `hotfix/*` yang diarahkan ke `main`).
   * Gunakan judul PR yang jelas sesuai konvensi commit.
   * Lengkapi deskripsi PR (ringkasan perubahan, referensi issue jika ada, dan cara menguji).
   * Lampirkan *screenshot* atau rekaman layar jika ada perubahan pada tampilan UI.
4. **Code Review:**
   * PR memerlukan minimal **1 approval** dari anggota tim lain sebelum dapat di-merge.
   * Jika reviewer memberikan saran perbaikan, lakukan perubahan di **branch yang sama**, lalu `commit` dan `push` ulang.
   * Pastikan seluruh pengecekan otomatis (CI/test/linter) lulus (**Passed**) dan semua komentar diskusi telah diselesaikan (**Resolved**).
5. **Merge & Bersihkan Branch:**
   * Setelah disetujui, lakukan merge sesuai kesepakatan tim.
   * Hapus branch fitur yang sudah selesai di-merge:
     ```bash
     git checkout develop
     git pull origin develop
     git branch -d feat/nama-fitur-kamu
     ```

---

## 6. Pelaporan Bug & Usulan Fitur (Issues)

* Gunakan fitur **Issues** di GitHub untuk melaporkan bug, mengusulkan fitur baru, atau melacak tugas.
* **Laporan Bug** sebaiknya memuat:
  1. Ringkasan masalah yang terjadi.
  2. Langkah-langkah mereproduksi bug (*Steps to Reproduce*).
  3. Hasil yang diharapkan (*Expected Behavior*) dan hasil yang sebenarnya terjadi (*Actual Behavior*).
  4. Pesan error, log, atau *screenshot* pendukung.
* **Usulan Fitur** sebaiknya memuat tujuan fitur, manfaat bagi pengguna, serta kriteria selesai (*Acceptance Criteria*).