# BDA Shooting Championship 2026

Website resmi kejuaraan menembak **BDA Shooting Championship 2026** dalam rangka memperingati **Anniversary Letting BDA 750 ke-7** di **Resimen I Pasukan Pelopor, Kedung Halang, Bogor**.

- **Frontend URL**: `https://bda-shooting-championship.github.io/page/`
- **Penyelenggara**: Brigade Diraya Adikara (BDA) 750 — Resimen I Pasukan Pelopor

---

## Fitur Utama

1. **Landing Page Interaktif**:
   - Hero Section dengan target pattern & ringkasan event
   - Countdown timer langsung menuju 17 Oktober 2026
   - Profil kegiatan, semboyan, dan statistik
   - Rincian 2 kategori pertandingan: **Pistol Presisi 20 Meter** & **Dueling Plat**
   - Rincian hadiah podium Juara 1, 2, 3 (Uang pembinaan total belasan juta + Trophy + Piagam/Sertifikat)
   - Jadwal lengkap pertandingan (Technical Meeting, Latihan Resmi, Babak Presisi, Babak Dueling Plat, Final & Upacara Penutupan)
   - Aturan pertandingan interaktif (Accordion): Pakaian, Senjata, Perlengkapan, Safety Rules 60°, Sanksi DQ/Penalty/DNS/DNF
   - Narahubung panitia dengan tautan langsung WhatsApp & informasi rekening BRI resmi

2. **Formulir Pendaftaran**:
   - Validasi data lengkap: Nama, Email, WhatsApp, Pangkat, NRP, Satuan/Club
   - Pemilihan kategori (Presisi 20M, Dueling Plat, atau keduanya) dengan kalkulasi otomatis biaya
   - Upload Foto KTA / Tanda Pengenal (preview & validasi ukuran file)
   - Upload Bukti Transfer Pembayaran
   - Halaman konfirmasi sukses pendaftaran dengan nomor registrasi unik

3. **Dashboard Admin**:
   - Proteksi login password admin
   - Rekap statistik pendaftar (Total, Lunas/Verified, Pending, Ditolak)
   - Pencarian real-time berdasarkan Nama, NRP, atau Satuan
   - Modal detail peserta untuk **mengecek bukti transfer & KTA**
   - Aksi verifikasi pembayaran (Lunas) & auto-generate **No. Peserta (BSC-001, BSC-002, dst.)**
   - Tombol **Kirim E-Ticket via WhatsApp** yang membuka WhatsApp secara otomatis dengan pesan resmi berisi rincian data peserta dan link E-Ticket
   - Ekspor data pendaftar ke file CSV / Excel

4. **E-Ticket Publik Unik & QR Code**:
   - URL unik per peserta (`/e-ticket/?id=BDA-XXXXXXXX`)
   - Menampilkan identitas peserta, No. Peserta resmi, kategori yang diikuti, status lunas, dan foto KTA
   - Dilengkapi **QR Code** hasil generate otomatis untuk scan validasi di lokasi lapangan
   - Desain ramah cetak (Print / Save to PDF) & screenshot sharing

5. **Backend Hostinger (PHP + MySQL)**:
   - Terletak di folder `hostinger/`
   - Skrip API RESTful lengkap: registrasi, daftar admin, update status, dan e-ticket publik
   - Penanganan upload gambar Base64 ke server Hostinger
   - Dilengkapi panduan setup lengkap di `hostinger/SETUP.md`

---

## Panduan Menjalankan Secara Lokal

```bash
# Clone repository
git clone https://github.com/BDA-shooting-championship/page.git
cd page

# Install dependensi
npm install

# Jalankan server pengembangan
npm run dev

# Build untuk produksi (static export ke out/)
npm run build
```

Buka `http://localhost:3000` pada peramban web Anda.
