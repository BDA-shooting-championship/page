'use client';

import React, { useState } from 'react';
import Link from 'next/link';
import { Target, Upload, CheckCircle2, AlertCircle, Loader2, ArrowLeft, Copy, Check, CreditCard, Shield } from 'lucide-react';
import { CONFIG } from '@/lib/config';
import { formatRupiah, fileToBase64 } from '@/lib/utils';
import { submitRegistration, RegistrationPayload } from '@/lib/api';

export default function DaftarPage() {
  const [formData, setFormData] = useState({
    nama: '',
    email: '',
    telepon: '',
    pangkat: '',
    nrp: '',
    satuan: '',
  });

  const [selectedKategori, setSelectedKategori] = useState<string[]>(['Pistol Presisi 20M']);
  const [fotoKTA, setFotoKTA] = useState<File | null>(null);
  const [previewKTA, setPreviewKTA] = useState<string | null>(null);
  const [buktiTransfer, setBuktiTransfer] = useState<File | null>(null);
  const [previewBukti, setPreviewBukti] = useState<string | null>(null);

  const [isSubmitting, setIsSubmitting] = useState(false);
  const [errorMsg, setErrorMsg] = useState<string | null>(null);
  const [successData, setSuccessData] = useState<{ registrationId: string; totalBiaya: number } | null>(null);
  const [copiedAccount, setCopiedAccount] = useState(false);

  // Hitung total biaya
  const totalBiaya = selectedKategori.length * CONFIG.REGISTRATION_FEE_PER_CATEGORY;

  const handleKategoriToggle = (kat: string) => {
    if (selectedKategori.includes(kat)) {
      if (selectedKategori.length === 1) return; // minimal 1 kategori
      setSelectedKategori(selectedKategori.filter(k => k !== kat));
    } else {
      setSelectedKategori([...selectedKategori, kat]);
    }
  };

  const handleKTAChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const file = e.target.files?.[0];
    if (!file) return;

    if (file.size > CONFIG.MAX_FILE_SIZE_BYTES) {
      alert('Ukuran file KTA melebihi batas maksimal 2MB');
      return;
    }

    setFotoKTA(file);
    setPreviewKTA(URL.createObjectURL(file));
  };

  const handleBuktiChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const file = e.target.files?.[0];
    if (!file) return;

    if (file.size > CONFIG.MAX_FILE_SIZE_BYTES) {
      alert('Ukuran file Bukti Transfer melebihi batas maksimal 2MB');
      return;
    }

    setBuktiTransfer(file);
    setPreviewBukti(URL.createObjectURL(file));
  };

  const handleCopyAccount = () => {
    navigator.clipboard.writeText(CONFIG.BANK_ACCOUNT.accountNumber);
    setCopiedAccount(true);
    setTimeout(() => setCopiedAccount(false), 2500);
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setErrorMsg(null);

    // Validasi Dasar
    if (!formData.nama.trim()) return setErrorMsg('Nama lengkap wajib diisi');
    if (!formData.email.trim()) return setErrorMsg('Email aktif wajib diisi');
    if (!formData.telepon.trim()) return setErrorMsg('Nomor WhatsApp wajib diisi');
    if (!formData.pangkat.trim()) return setErrorMsg('Pangkat wajib diisi');
    if (!formData.nrp.trim()) return setErrorMsg('NRP wajib diisi');
    if (!formData.satuan.trim()) return setErrorMsg('Satuan atau Club wajib diisi');
    if (selectedKategori.length === 0) return setErrorMsg('Pilih minimal 1 kategori lomba');
    if (!fotoKTA) return setErrorMsg('Foto KTA / Tanda Pengenal wajib diunggah');

    setIsSubmitting(true);

    try {
      // Encode file to Base64
      const ktaBase64 = fotoKTA ? await fileToBase64(fotoKTA) : null;
      const buktiBase64 = buktiTransfer ? await fileToBase64(buktiTransfer) : null;

      const payload: RegistrationPayload = {
        ...formData,
        kategori: selectedKategori,
        fotoKTA: ktaBase64,
        buktiTransfer: buktiBase64,
      };

      const res = await submitRegistration(payload);
      if (res.success) {
        setSuccessData({
          registrationId: res.registrationId,
          totalBiaya,
        });
      } else {
        setErrorMsg(res.message || 'Gagal mengirim pendaftaran');
      }
    } catch (err: any) {
      setErrorMsg(err.message || 'Terjadi kesalahan pada koneksi sistem');
    } finally {
      setIsSubmitting(false);
    }
  };

  // Tampilan Sukses Setelah Pendaftaran
  if (successData) {
    return (
      <div className="pt-32 pb-24 min-h-screen bg-neutral-50 dark:bg-[#0f1419]">
        <div className="max-w-2xl mx-auto px-4 sm:px-6">
          <div className="bg-white dark:bg-[#1a2332] rounded-3xl border border-neutral-200 dark:border-neutral-800 p-8 sm:p-12 shadow-xl text-center">
            <div className="w-16 h-16 rounded-full bg-emerald-500/10 text-emerald-500 flex items-center justify-center mx-auto mb-6">
              <CheckCircle2 className="w-10 h-10 stroke-[2.2]" />
            </div>

            <span className="text-xs uppercase font-semibold tracking-wider text-emerald-600 dark:text-emerald-400 bg-emerald-500/10 px-3 py-1 rounded-full">
              Pendaftaran Berhasil Terkirim
            </span>

            <h2 className="font-heading font-bold text-3xl sm:text-4xl text-neutral-900 dark:text-white uppercase tracking-tight mt-4">
              Terima Kasih, {formData.nama}!
            </h2>

            <p className="text-sm text-neutral-600 dark:text-neutral-400 mt-2 max-w-md mx-auto">
              Data pendaftaran Anda telah tersimpan ke dalam database panitia BDA Shooting Championship 2026.
            </p>

            {/* Kotak Ringkasan */}
            <div className="my-8 p-6 rounded-2xl bg-neutral-50 dark:bg-[#151c27] border border-neutral-200 dark:border-neutral-800 text-left space-y-3">
              <div className="flex justify-between items-center pb-3 border-b border-neutral-200 dark:border-neutral-800/80">
                <span className="text-xs text-neutral-500">ID Registrasi</span>
                <span className="font-mono font-bold text-sm text-brand-500">{successData.registrationId}</span>
              </div>
              <div className="flex justify-between items-center text-xs">
                <span className="text-neutral-500">Pangkat / NRP:</span>
                <span className="font-semibold text-neutral-800 dark:text-neutral-200">{formData.pangkat} / {formData.nrp}</span>
              </div>
              <div className="flex justify-between items-center text-xs">
                <span className="text-neutral-500">Satuan / Club:</span>
                <span className="font-semibold text-neutral-800 dark:text-neutral-200">{formData.satuan}</span>
              </div>
              <div className="flex justify-between items-center text-xs">
                <span className="text-neutral-500">Kategori Lomba:</span>
                <span className="font-semibold text-neutral-800 dark:text-neutral-200">{selectedKategori.join(', ')}</span>
              </div>
              <div className="flex justify-between items-center pt-3 border-t border-neutral-200 dark:border-neutral-800/80 text-sm font-semibold">
                <span className="text-neutral-600 dark:text-neutral-400">Total Biaya:</span>
                <span className="text-brand-500 font-bold">{formatRupiah(successData.totalBiaya)}</span>
              </div>
            </div>

            <div className="p-4 rounded-xl bg-amber-500/10 border border-amber-500/20 text-xs text-amber-800 dark:text-amber-300 text-left mb-8">
              <strong>Tahap Berikutnya:</strong> Panitia akan memeriksa bukti transfer pembayaran Anda. Setelah dikonfirmasi, panitia akan mengirimkan pesan resmi beserta tautan <strong>E-Ticket</strong> dan <strong>QR Code</strong> ke nomor WhatsApp <strong>{formData.telepon}</strong>.
            </div>

            <div className="flex flex-col sm:flex-row items-center justify-center gap-4">
              <Link
                href="/"
                className="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl text-sm font-semibold bg-brand-500 text-white hover:bg-brand-600 transition-colors shadow-md"
              >
                <span>Kembali ke Beranda</span>
              </Link>
              <Link
                href={`/e-ticket/?id=${encodeURIComponent(successData.registrationId)}`}
                className="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl text-sm font-semibold border border-neutral-300 dark:border-neutral-700 text-neutral-700 dark:text-neutral-200 hover:bg-neutral-100 dark:hover:bg-neutral-800 transition-colors"
              >
                <span>Cek Status E-Ticket</span>
              </Link>
            </div>
          </div>
        </div>
      </div>
    );
  }

  return (
    <div className="pt-32 pb-24 min-h-screen bg-neutral-50 dark:bg-[#0f1419]">
      <div className="max-w-3xl mx-auto px-4 sm:px-6">
        {/* Navigasi Balik */}
        <Link
          href="/"
          className="inline-flex items-center gap-2 text-xs font-semibold text-neutral-600 dark:text-neutral-400 hover:text-brand-500 dark:hover:text-brand-400 mb-6 transition-colors"
        >
          <ArrowLeft className="w-4 h-4" />
          <span>Kembali ke Beranda</span>
        </Link>

        {/* Header Formulir */}
        <div className="bg-white dark:bg-[#1a2332] rounded-3xl border border-neutral-200 dark:border-neutral-800 p-8 sm:p-10 shadow-sm mb-8">
          <div className="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-brand-500/10 text-brand-600 dark:text-brand-400 border border-brand-500/20 mb-3">
            <Target className="w-3.5 h-3.5" />
            <span>Formulir Pendaftaran Resmi</span>
          </div>
          <h1 className="font-heading font-bold text-3xl sm:text-4xl text-neutral-900 dark:text-white uppercase tracking-tight">
            Pendaftaran Peserta BDA 2026
          </h1>
          <p className="text-sm text-neutral-600 dark:text-neutral-400 mt-2 leading-relaxed">
            Silakan lengkapi formulir di bawah ini dengan data yang sah dan unggah foto KTA serta bukti transfer pendaftaran.
          </p>

          {/* Kotak Informasi Rekening Pembayaran */}
          <div className="mt-6 p-5 rounded-2xl bg-neutral-50 dark:bg-[#151c27] border border-neutral-200 dark:border-neutral-800">
            <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
              <div>
                <span className="text-[11px] uppercase tracking-wider font-semibold text-brand-600 dark:text-brand-400 block mb-1">
                  Biaya Pendaftaran: {formatRupiah(CONFIG.REGISTRATION_FEE_PER_CATEGORY)} / Kategori
                </span>
                <span className="text-xs text-neutral-600 dark:text-neutral-400 block">
                  Transfer ke <strong>Bank {CONFIG.BANK_ACCOUNT.bank}</strong>: <code className="font-mono font-bold text-neutral-900 dark:text-white">{CONFIG.BANK_ACCOUNT.accountNumber}</code> (a.n. {CONFIG.BANK_ACCOUNT.accountName})
                </span>
              </div>

              <button
                type="button"
                onClick={handleCopyAccount}
                className="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-white dark:bg-[#1e293b] border border-neutral-200 dark:border-neutral-700 text-neutral-700 dark:text-neutral-200 hover:text-brand-500 transition-colors shrink-0"
              >
                {copiedAccount ? <Check className="w-3.5 h-3.5 text-emerald-500" /> : <Copy className="w-3.5 h-3.5" />}
                <span>{copiedAccount ? 'Tersalin' : 'Salin Rekening'}</span>
              </button>
            </div>
          </div>
        </div>

        {/* Error Alert */}
        {errorMsg && (
          <div className="p-4 rounded-2xl bg-red-500/10 border border-red-500/20 text-sm text-red-600 dark:text-red-400 flex items-start gap-3 mb-8">
            <AlertCircle className="w-5 h-5 shrink-0 mt-0.5" />
            <span>{errorMsg}</span>
          </div>
        )}

        {/* Form Isi Data */}
        <form onSubmit={handleSubmit} className="bg-white dark:bg-[#1a2332] rounded-3xl border border-neutral-200 dark:border-neutral-800 p-8 sm:p-10 shadow-sm space-y-6">
          {/* 1. Nama Lengkap */}
          <div>
            <label className="block text-xs font-semibold uppercase tracking-wider text-neutral-700 dark:text-neutral-300 mb-2">
              1. Nama Lengkap *
            </label>
            <input
              type="text"
              required
              value={formData.nama}
              onChange={(e) => setFormData({ ...formData, nama: e.target.value })}
              placeholder="Contoh: Huggies Yustisio"
              className="w-full px-4 py-3 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-neutral-50/50 dark:bg-[#151c27] text-neutral-900 dark:text-white placeholder-neutral-400 focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all text-sm"
            />
          </div>

          {/* 2 & 3. Email & No. Telp (WhatsApp) */}
          <div className="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
              <label className="block text-xs font-semibold uppercase tracking-wider text-neutral-700 dark:text-neutral-300 mb-2">
                2. Email Aktif *
              </label>
              <input
                type="email"
                required
                value={formData.email}
                onChange={(e) => setFormData({ ...formData, email: e.target.value })}
                placeholder="nama@email.com"
                className="w-full px-4 py-3 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-neutral-50/50 dark:bg-[#151c27] text-neutral-900 dark:text-white placeholder-neutral-400 focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all text-sm"
              />
            </div>

            <div>
              <label className="block text-xs font-semibold uppercase tracking-wider text-neutral-700 dark:text-neutral-300 mb-2">
                3. No. Telepon / WhatsApp *
              </label>
              <input
                type="tel"
                required
                value={formData.telepon}
                onChange={(e) => setFormData({ ...formData, telepon: e.target.value })}
                placeholder="Contoh: 085272377704"
                className="w-full px-4 py-3 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-neutral-50/50 dark:bg-[#151c27] text-neutral-900 dark:text-white placeholder-neutral-400 focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all text-sm"
              />
              <span className="text-[11px] text-neutral-500 mt-1 block">E-Ticket resmi akan dikirimkan ke nomor ini</span>
            </div>
          </div>

          {/* 4 & 5. Pangkat & NRP */}
          <div className="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
              <label className="block text-xs font-semibold uppercase tracking-wider text-neutral-700 dark:text-neutral-300 mb-2">
                4. Pangkat *
              </label>
              <input
                type="text"
                required
                value={formData.pangkat}
                onChange={(e) => setFormData({ ...formData, pangkat: e.target.value })}
                placeholder="Contoh: Briptu / Bharaka / Ipda"
                className="w-full px-4 py-3 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-neutral-50/50 dark:bg-[#151c27] text-neutral-900 dark:text-white placeholder-neutral-400 focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all text-sm"
              />
            </div>

            <div>
              <label className="block text-xs font-semibold uppercase tracking-wider text-neutral-700 dark:text-neutral-300 mb-2">
                5. NRP / NIP *
              </label>
              <input
                type="text"
                required
                value={formData.nrp}
                onChange={(e) => setFormData({ ...formData, nrp: e.target.value })}
                placeholder="Contoh: 00120207"
                className="w-full px-4 py-3 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-neutral-50/50 dark:bg-[#151c27] text-neutral-900 dark:text-white placeholder-neutral-400 focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all text-sm"
              />
            </div>
          </div>

          {/* 6. Satuan / Club */}
          <div>
            <label className="block text-xs font-semibold uppercase tracking-wider text-neutral-700 dark:text-neutral-300 mb-2">
              6. Satuan / Club Menembak *
            </label>
            <input
              type="text"
              required
              value={formData.satuan}
              onChange={(e) => setFormData({ ...formData, satuan: e.target.value })}
              placeholder="Contoh: Batalyon A Resimen I Pasukan Pelopor / BDA 750"
              className="w-full px-4 py-3 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-neutral-50/50 dark:bg-[#151c27] text-neutral-900 dark:text-white placeholder-neutral-400 focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all text-sm"
            />
          </div>

          {/* 7. Kategori Pertandingan */}
          <div className="pt-2">
            <label className="block text-xs font-semibold uppercase tracking-wider text-neutral-700 dark:text-neutral-300 mb-3">
              7. Kategori Pertandingan (Bisa pilih 1 atau keduanya) *
            </label>
            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
              {[
                { id: 'Pistol Presisi 20M', title: 'Pistol Presisi 20M', desc: 'Kelas Individu (Sasaran Lesan Ring)' },
                { id: 'Dueling Plat', title: 'Dueling Plat', desc: 'Kelas Individu (5 Plat Bulat + 1 Popper)' },
              ].map((kat) => {
                const isChecked = selectedKategori.includes(kat.id);
                return (
                  <button
                    key={kat.id}
                    type="button"
                    onClick={() => handleKategoriToggle(kat.id)}
                    className={`p-4 rounded-2xl border text-left flex items-start justify-between transition-all ${
                      isChecked
                        ? 'border-brand-500 bg-brand-500/5 dark:bg-brand-500/10 ring-2 ring-brand-500/20'
                        : 'border-neutral-200 dark:border-neutral-800 bg-neutral-50/50 dark:bg-[#151c27] hover:border-neutral-300'
                    }`}
                  >
                    <div>
                      <h4 className="font-semibold text-sm text-neutral-900 dark:text-white">
                        {kat.title}
                      </h4>
                      <p className="text-xs text-neutral-500 dark:text-neutral-400 mt-0.5">
                        {kat.desc}
                      </p>
                      <span className="inline-block mt-2 font-mono font-semibold text-xs text-brand-600 dark:text-brand-400">
                        {formatRupiah(CONFIG.REGISTRATION_FEE_PER_CATEGORY)}
                      </span>
                    </div>

                    <div
                      className={`w-5 h-5 rounded-md flex items-center justify-center border transition-colors ${
                        isChecked
                          ? 'bg-brand-500 border-brand-500 text-white'
                          : 'border-neutral-300 dark:border-neutral-600'
                      }`}
                    >
                      {isChecked && <Check className="w-3.5 h-3.5 stroke-[3]" />}
                    </div>
                  </button>
                );
              })}
            </div>

            {/* Total Biaya Banner */}
            <div className="mt-4 p-4 rounded-xl bg-neutral-100 dark:bg-[#161e2b] flex items-center justify-between">
              <span className="text-xs font-semibold text-neutral-600 dark:text-neutral-300">
                Total Biaya Pendaftaran: ({selectedKategori.length} Kategori)
              </span>
              <span className="font-heading font-bold text-xl text-brand-500">
                {formatRupiah(totalBiaya)}
              </span>
            </div>
          </div>

          {/* 8. Upload Foto KTA */}
          <div className="pt-2">
            <label className="block text-xs font-semibold uppercase tracking-wider text-neutral-700 dark:text-neutral-300 mb-2">
              8. Upload Foto KTA / Kartu Anggota * (Maks 2MB)
            </label>
            <div className="flex flex-col sm:flex-row items-center gap-4">
              <label className="w-full sm:flex-1 flex flex-col items-center justify-center p-6 rounded-2xl border-2 border-dashed border-neutral-300 dark:border-neutral-700 hover:border-brand-500 dark:hover:border-brand-500 transition-colors cursor-pointer bg-neutral-50/50 dark:bg-[#151c27]">
                <Upload className="w-6 h-6 text-neutral-400 mb-2" />
                <span className="text-xs font-semibold text-neutral-700 dark:text-neutral-300">
                  {fotoKTA ? fotoKTA.name : 'Pilih File Foto KTA (JPG, PNG, WebP)'}
                </span>
                <span className="text-[11px] text-neutral-400 mt-1">Maksimal ukuran file 2MB</span>
                <input
                  type="file"
                  accept="image/jpeg,image/png,image/webp"
                  onChange={handleKTAChange}
                  className="hidden"
                />
              </label>

              {previewKTA && (
                <div className="w-24 h-24 rounded-xl border border-neutral-200 dark:border-neutral-700 overflow-hidden shrink-0 bg-neutral-100 relative">
                  {/* eslint-disable-next-line @next/next/no-img-element */}
                  <img src={previewKTA} alt="Preview KTA" className="w-full h-full object-cover" />
                </div>
              )}
            </div>
          </div>

          {/* 9. Upload Bukti Transfer */}
          <div className="pt-2">
            <label className="block text-xs font-semibold uppercase tracking-wider text-neutral-700 dark:text-neutral-300 mb-2">
              9. Upload Bukti Transfer Pembayaran * (Maks 2MB)
            </label>
            <div className="flex flex-col sm:flex-row items-center gap-4">
              <label className="w-full sm:flex-1 flex flex-col items-center justify-center p-6 rounded-2xl border-2 border-dashed border-neutral-300 dark:border-neutral-700 hover:border-brand-500 dark:hover:border-brand-500 transition-colors cursor-pointer bg-neutral-50/50 dark:bg-[#151c27]">
                <CreditCard className="w-6 h-6 text-neutral-400 mb-2" />
                <span className="text-xs font-semibold text-neutral-700 dark:text-neutral-300">
                  {buktiTransfer ? buktiTransfer.name : 'Pilih File Bukti Transfer (JPG, PNG, WebP)'}
                </span>
                <span className="text-[11px] text-neutral-400 mt-1">Maksimal ukuran file 2MB</span>
                <input
                  type="file"
                  accept="image/jpeg,image/png,image/webp"
                  onChange={handleBuktiChange}
                  className="hidden"
                />
              </label>

              {previewBukti && (
                <div className="w-24 h-24 rounded-xl border border-neutral-200 dark:border-neutral-700 overflow-hidden shrink-0 bg-neutral-100 relative">
                  {/* eslint-disable-next-line @next/next/no-img-element */}
                  <img src={previewBukti} alt="Preview Bukti Transfer" className="w-full h-full object-cover" />
                </div>
              )}
            </div>
          </div>

          {/* Submit Button */}
          <div className="pt-4">
            <button
              type="submit"
              disabled={isSubmitting}
              className="w-full py-4 rounded-xl font-semibold text-sm bg-brand-500 hover:bg-brand-600 disabled:opacity-50 text-white shadow-lg shadow-brand-500/25 transition-all flex items-center justify-center gap-2"
            >
              {isSubmitting ? (
                <>
                  <Loader2 className="w-5 h-5 animate-spin" />
                  <span>Memproses Pendaftaran...</span>
                </>
              ) : (
                <>
                  <Target className="w-5 h-5" />
                  <span>Kirim Formulir Pendaftaran Sekarang</span>
                </>
              )}
            </button>
          </div>
        </form>
      </div>
    </div>
  );
}
