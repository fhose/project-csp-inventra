# Inventra – Sistem Manajemen Inventaris & Peminjaman Barang Lab

Inventra adalah aplikasi web berbasis Laravel dan React yang dirancang untuk memudahkan pengelolaan inventaris dan proses peminjaman barang laboratorium oleh admin, asisten, maupun mahasiswa. Aplikasi ini mendukung sistem login multi-role, pengelolaan stok barang, serta fitur perpanjangan dan penalti otomatis jika terjadi keterlambatan.

---

## Struktur Direktori
```
project-csp-inventra/
├──> backend-inventory-csp/ # Laravel Backend
├──> frontend-inventaris-csp/ # React Frontend
└──> .gitignore 
```

---

## Teknologi yang Digunakan

### Backend
- **Laravel 11+**
- Laravel Sanctum (autentikasi API)
- Eloquent ORM
- RESTful API
- Role-based access control (Middleware)

### Frontend
- **ReactJS**
- Tailwind CSS
- Axios
- SweetAlert2 (popup)
- React Router DOM
- Lucide React package

---

##  Cara Menjalankan Project

###  1.  Laravel Backend

```bash
cd backend-inventory-csp
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```
---

##  Teknologi yang Digunakan

### Backend
- Laravel 11+
- Laravel Sanctum (autentikasi API)
- RESTful API
- Role-based access control

### Frontend
- ReactJS
- Tailwind CSS
- Axios 
- SweetAlert2 (popup)
- React Router DOM
- Lucide React package

---


### 2. Menjalankan React Frontend

```bash
cd backend-inventory-csp
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve

npm run dev

NOTE:
->login info:
admin :
email: admin@lab.inforpcu.petra.ac.id
pass: admin123

user :
email: c14220151@john.petra.ac.id
pass: hose123
```
---
## Fitur Utama
- Autentikasi dengan Laravel Sanctum

- CRUD Barang Inventaris (kode unik, stok, kondisi, lokasi)

- Peminjaman Barang (dengan sistem penalti & batas tanggal)

- Perpanjangan masa pinjam (dengan approval admin)

- Statistik Peminjaman di Dashboard Admin

- Validasi stok dan pencegahan duplikasi peminjaman
