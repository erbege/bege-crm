# 🚀 Bege CRM - ISP Billing & Network Management System

[![Laravel 12](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel)](https://laravel.com)
[![Livewire 3](https://img.shields.io/badge/Livewire-3.x-FB70A9?style=for-the-badge&logo=livewire)](https://livewire.laravel.com)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-4.0-38B2AC?style=for-the-badge&logo=tailwind-css)](https://tailwindcss.com)
[![PHP 8.2](https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php)](https://php.net)

**Bege CRM** adalah solusi manajemen terintegrasi yang dirancang khusus untuk Internet Service Provider (ISP). Sistem ini menggabungkan manajemen pelanggan (CRM), sistem penagihan otomatis (Billing), dan manajemen infrastruktur jaringan (Network Management) dalam satu platform yang modern dan responsif.

---

## ✨ Keunggulan Utama

- **Real-time Monitoring**: Dashboard interaktif yang menyajikan statistik pendapatan, status pelanggan, dan performa hotspot secara real-time.
- **Automated Billing**: Generasi invoice rutin otomatis untuk meminimalisir kesalahan manusia dan mempercepat proses penagihan.
- **Seamless Network Integration**: Integrasi mendalam dengan Mikrotik via RouterOS API dan manajemen OLT untuk kontrol jaringan yang presisi.
- **Premium User Experience**: Antarmuka modern yang dibangun dengan Livewire 3 dan Tailwind CSS, memberikan pengalaman aplikasi desktop di dalam browser.
- **Scalable Architecture**: Siap mendukung ekspansi bisnis Anda, mulai dari satu NAS hingga manajemen multi-lokasi dan multi-perangkat.

---

## 🛠️ Fitur Unggulan

### 📊 Dashboard & Reporting
- Visualisasi data pendapatan harian dan bulanan.
- Statistik pelanggan aktif, tersuspensi, dan pendaftar baru.
- Ringkasan transaksi hotspot dan status invoice tertunggak.

### 👤 Customer & Subscription Management
- Database pelanggan terpusat dengan informasi detail.
- Manajemen paket layanan (Bandwidth Profile) yang fleksibel.
- Pelacakan riwayat langganan dan status coverage area.

### 💰 Automated Billing System
- Pembuatan invoice otomatis setiap bulan.
- Pencatatan riwayat pembayaran dan integrasi rekening bank.
- Pengingat jatuh tempo dan pelacakan invoice overdue.

### 🌐 Network & NAS Management
- Manajemen NAS Server dan integrasi Mikrotik API.
- Manajemen OLT (Optical Line Terminal).
- Konfigurasi Radius Server untuk autentikasi yang aman.

### 🎫 Hotspot & Voucher System
- Pembuatan profil hotspot dengan limitasi bandwidth dan waktu.
- Generator voucher massal dengan template yang dapat dikustomisasi.
- Laporan penjualan voucher dan statistik penggunaan hotspot.

### 📩 Communication & Support
- Integrasi WhatsApp Notification untuk pengiriman invoice dan informasi layanan.
- Sistem Ticketing untuk penanganan keluhan pelanggan secara terstruktur.

---

## 💻 Stack Teknologi

| Komponen | Teknologi |
| --- | --- |
| **Backend** | Laravel 12.x |
| **Frontend Logic** | Livewire 3.x, Alpine.js |
| **Styling** | Tailwind CSS 4.0 |
| **Database** | MySQL / MariaDB |
| **Caching & Queue** | Redis |
| **Integrasi** | Mikrotik RouterOS API, FreeRadius |
| **Build Tool** | Vite |

---

## 🚀 Panduan Instalasi

### Prasyarat
- PHP >= 8.2
- Composer
- Node.js & NPM
- MySQL/MariaDB
- Redis (Opsional, untuk performa lebih baik)

### Langkah-langkah
1. **Clone repositori**
   ```bash
   git clone https://github.com/username/bege-crm.git
   cd bege-crm
   ```

2. **Instal dependensi PHP**
   ```bash
   composer install
   ```

3. **Instal dependensi Frontend**
   ```bash
   npm install
   npm run build
   ```

4. **Konfigurasi Environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   *Sesuaikan pengaturan database dan Mikrotik API di file `.env`.*

5. **Migrasi Database**
   ```bash
   php artisan migrate --seed
   ```

6. **Jalankan Aplikasi**
   ```bash
   php artisan serve
   ```

---

## 📸 Screenshot
*(Tambahkan screenshot aplikasi di sini untuk memberikan gambaran visual kepada pengguna)*

---

## 🤝 Kontribusi
Kontribusi selalu terbuka! Silakan lakukan *fork* dan kirimkan *pull request* untuk fitur baru atau perbaikan bug.

## 📄 Lisensi
Proyek ini dilisensikan di bawah [MIT License](LICENSE).

---
Built with ❤️ by **CodeCrafter Studio**
