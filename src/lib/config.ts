// Konfigurasi Terpusat BDA Shooting Championship 2026

export const CONFIG = {
  EVENT_NAME: "BDA Shooting Championship 2026",
  SUBTITLE: "Kejuaraan Menembak Pistol Presisi 20M & Dueling Plat",
  ORGANIZER: "Brigade Diraya Adikara (BDA) 750",
  UNIT: "Resimen I Pasukan Pelopor - Kedung Halang, Bogor",
  EVENT_DATE_START: "2026-10-17T08:00:00+07:00",
  EVENT_DATE_END: "2026-10-18T17:00:00+07:00",
  LOCATION: "Lapangan Tembak Shooting House Resimen I Pasukan Pelopor, Kedung Halang, Bogor",
  
  // Biaya Pendaftaran
  REGISTRATION_FEE_PER_CATEGORY: 200000, // Rp 200.000 per kategori
  
  // Info Pembayaran
  BANK_ACCOUNT: {
    bank: "BRI",
    accountNumber: "053801071906503",
    accountName: "Ahyandi Hi Karim",
  },
  
  // Kontak Panitia
  CONTACTS: [
    {
      role: "Materi & Teknis",
      name: "Briptu Ady",
      phone: "085283525761",
      displayPhone: "0852-8352-5761",
    },
    {
      role: "Ketua Pelaksana / Teknis",
      name: "Briptu Huges Yustisio",
      phone: "085272377704",
      displayPhone: "0852-7237-7704",
    },
    {
      role: "Pendaftaran",
      name: "Briptu Rully",
      phone: "085775015786",
      displayPhone: "0857-7501-5786",
    },
    {
      role: "Pendaftaran",
      name: "Briptu Zyaldi",
      phone: "082134651503",
      displayPhone: "0821-3465-1503",
    },
  ],

  // URL API Backend (Hostinger)
  API_BASE_URL: process.env.NEXT_PUBLIC_API_URL || "",
  
  // Token Akses Admin untuk API
  ADMIN_TOKEN: process.env.NEXT_PUBLIC_ADMIN_TOKEN || "bda_secret_token_2026_supersecure",
  
  // Password login dashboard admin di web
  ADMIN_PASSWORD: process.env.NEXT_PUBLIC_ADMIN_PASSWORD || "bda2026admin",
  
  // Maksimal ukuran upload (bytes)
  MAX_FILE_SIZE_BYTES: 2 * 1024 * 1024, // 2MB
};
