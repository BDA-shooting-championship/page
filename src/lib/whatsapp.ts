// WhatsApp Message Generator for E-Ticket & Confirmation
import { CONFIG } from './config';
import { normalizePhoneForWA, formatRupiah } from './utils';

export interface RegistrationRecord {
  id?: number;
  registration_id: string;
  no_peserta?: string | null;
  nama: string;
  email: string;
  telepon: string;
  pangkat: string;
  nrp: string;
  satuan: string;
  kategori: string;
  kta_url?: string | null;
  bukti_url?: string | null;
  status: 'Pending' | 'Verified' | 'Rejected';
  admin_notes?: string | null;
  created_at?: string;
}

export function buildWhatsAppTicketUrl(reg: RegistrationRecord): string {
  // Determine site base URL for E-Ticket link
  let baseUrl = '';
  if (typeof window !== 'undefined') {
    baseUrl = window.location.origin + (process.env.NODE_ENV === 'production' ? '/page' : '');
  } else {
    baseUrl = 'https://bda-shooting-championship.github.io/page';
  }

  const eTicketUrl = `${baseUrl}/e-ticket/?id=${encodeURIComponent(reg.registration_id)}`;

  // Hitung perkiraan biaya berdasarkan kategori yang dipilih
  const categories = reg.kategori.split(',').map(c => c.trim()).filter(Boolean);
  const totalBiaya = categories.length * CONFIG.REGISTRATION_FEE_PER_CATEGORY;

  const message = `🎯 *BDA SHOOTING CHAMPIONSHIP 2026*
━━━━━━━━━━━━━━━━━━━━━━━━━━━━
*PENGESAHAN PENDAFTARAN & E-TICKET*

Yth. *${reg.nama}* (${reg.pangkat}),
Pembayaran pendaftaran Anda telah berhasil *DIVERIFIKASI & DIKONFIRMASI (LUNAS)*.

📋 *Rincian Data Peserta:*
• *No. Peserta* : *${reg.no_peserta || '-'}*
• *ID Registrasi*: ${reg.registration_id}
• *Pangkat/NRP*  : ${reg.pangkat} / ${reg.nrp}
• *Satuan/Club*  : ${reg.satuan}
• *Kategori*     : ${reg.kategori}
• *Status Biaya* : LUNAS (${formatRupiah(totalBiaya)})

🎫 *E-Ticket & QR Code Resmi:*
Silakan akses dan simpan E-Ticket resmi Anda pada tautan berikut:
👉 ${eTicketUrl}

📌 *Catatan Penting:*
1. Simpan atau tangkap layar (screenshot) E-Ticket dan QR Code tersebut.
2. Tunjukkan E-Ticket pada saat registrasi ulang di lokasi pertandingan.
3. *Tempat*: ${CONFIG.LOCATION}
4. *Jadwal*: 17 — 18 Oktober 2026

Selamat bertanding dan junjung tinggi sportivitas! 🏆

_Panitia BDA Shooting Championship 2026_
_${CONFIG.ORGANIZER} - ${CONFIG.UNIT}_`;

  const phone = normalizePhoneForWA(reg.telepon);
  return `https://wa.me/${phone}?text=${encodeURIComponent(message)}`;
}
