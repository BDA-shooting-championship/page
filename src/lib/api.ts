// API Service Layer (Hostinger Backend with LocalStorage Fallback Support)
import { CONFIG } from './config';
import { RegistrationRecord } from './whatsapp';

export interface RegistrationPayload {
  nama: string;
  email: string;
  telepon: string;
  pangkat: string;
  nrp: string;
  satuan: string;
  kategori: string[];
  fotoKTA: { data: string; mimeType: string; name: string } | null;
  buktiTransfer: { data: string; mimeType: string; name: string } | null;
}

const LOCAL_STORAGE_KEY = 'bda_local_registrations_v1';

// Helper storage lokal untuk testing/demo offline
function getLocalData(): RegistrationRecord[] {
  if (typeof window === 'undefined') return [];
  try {
    const raw = localStorage.getItem(LOCAL_STORAGE_KEY);
    return raw ? JSON.parse(raw) : [];
  } catch {
    return [];
  }
}

function saveLocalData(records: RegistrationRecord[]) {
  if (typeof window === 'undefined') return;
  try {
    localStorage.setItem(LOCAL_STORAGE_KEY, JSON.stringify(records));
  } catch (e) {
    console.error('Failed to save to localStorage', e);
  }
}

export async function submitRegistration(payload: RegistrationPayload): Promise<{ success: boolean; registrationId: string; message?: string }> {
  // Jika URL API Hostinger dikonfigurasi, coba kirim ke server
  if (CONFIG.API_BASE_URL) {
    try {
      const response = await fetch(`${CONFIG.API_BASE_URL}/api/register.php`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload),
      });

      const json = await response.json();
      if (!response.ok || !json.success) {
        throw new Error(json.error || 'Terjadi kesalahan saat memproses pendaftaran');
      }
      return json;
    } catch (err: any) {
      console.warn('Hostinger API unreachable or error, falling back to local storage preview', err);
      // Jika server gagal, lanjutkan fallback ke local storage agar demo/testing tetap berfungsi
    }
  }

  // Fallback Local Storage Mode
  const existing = getLocalData();
  const duplicate = existing.find(r => r.nrp.toLowerCase() === payload.nrp.trim().toLowerCase());
  if (duplicate) {
    throw new Error(`NRP ${payload.nrp} sudah terdaftar sebelumnya`);
  }

  const regId = 'BDA-' + Math.random().toString(36).substring(2, 10).toUpperCase();
  const newRecord: RegistrationRecord = {
    id: existing.length + 1,
    registration_id: regId,
    no_peserta: null,
    nama: payload.nama,
    email: payload.email,
    telepon: payload.telepon,
    pangkat: payload.pangkat,
    nrp: payload.nrp,
    satuan: payload.satuan,
    kategori: payload.kategori.join(', '),
    kta_url: payload.fotoKTA?.data || null,
    bukti_url: payload.buktiTransfer?.data || null,
    status: 'Pending',
    admin_notes: '',
    created_at: new Date().toISOString(),
  };

  existing.unshift(newRecord);
  saveLocalData(existing);

  return {
    success: true,
    registrationId: regId,
    message: 'Pendaftaran berhasil dikirim. Panitia akan memverifikasi pembayaran Anda.',
  };
}

export async function fetchRegistrations(token: string): Promise<RegistrationRecord[]> {
  if (CONFIG.API_BASE_URL) {
    try {
      const response = await fetch(`${CONFIG.API_BASE_URL}/api/registrations.php?token=${encodeURIComponent(token)}`);
      const json = await response.json();
      if (response.ok && json.success) {
        return json.data;
      }
    } catch (err) {
      console.warn('Failed to fetch from Hostinger API, loading local fallback data', err);
    }
  }

  // Fallback data
  return getLocalData();
}

export async function updateRegistrationStatus(
  registrationId: string,
  status: 'Pending' | 'Verified' | 'Rejected',
  notes: string = '',
  token: string
): Promise<{ success: boolean; noPeserta?: string | null }> {
  if (CONFIG.API_BASE_URL) {
    try {
      const response = await fetch(`${CONFIG.API_BASE_URL}/api/status.php`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          token,
          registrationId,
          status,
          notes,
        }),
      });
      const json = await response.json();
      if (response.ok && json.success) {
        return json;
      }
    } catch (err) {
      console.warn('Failed to update via Hostinger API, updating locally', err);
    }
  }

  // Fallback update
  const list = getLocalData();
  const index = list.findIndex(r => r.registration_id === registrationId);
  if (index === -1) {
    throw new Error('Data pendaftaran tidak ditemukan');
  }

  let noPeserta = list[index].no_peserta;
  if (status === 'Verified' && !noPeserta) {
    const verifiedCount = list.filter(r => r.no_peserta && r.no_peserta.startsWith('BSC-')).length;
    noPeserta = `BSC-${String(verifiedCount + 1).padStart(3, '0')}`;
  }

  list[index].status = status;
  list[index].admin_notes = notes;
  list[index].no_peserta = noPeserta;
  saveLocalData(list);

  return { success: true, noPeserta };
}

export async function fetchETicket(registrationId: string): Promise<RegistrationRecord> {
  if (CONFIG.API_BASE_URL) {
    try {
      const response = await fetch(`${CONFIG.API_BASE_URL}/api/eticket.php?id=${encodeURIComponent(registrationId)}`);
      const json = await response.json();
      if (response.ok && json.success) {
        return json.data;
      } else {
        throw new Error(json.error || 'E-Ticket tidak dapat dimuat');
      }
    } catch (err: any) {
      console.warn('API error, falling back to local storage', err);
    }
  }

  // Fallback lookup
  const list = getLocalData();
  const record = list.find(r => r.registration_id === registrationId);
  if (!record) {
    throw new Error('Data E-Ticket tidak ditemukan. Pastikan ID pendaftaran sudah benar.');
  }
  if (record.status !== 'Verified') {
    throw new Error('E-Ticket belum aktif karena pembayaran masih dalam proses verifikasi oleh panitia.');
  }

  return record;
}
