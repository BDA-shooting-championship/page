'use client';

import React, { useState, useEffect, Suspense } from 'react';
import { useSearchParams } from 'next/navigation';
import Link from 'next/link';
import QRCode from 'qrcode';
import { Target, ShieldCheck, CheckCircle2, AlertCircle, Printer, ArrowLeft, Loader2, Download, Calendar, MapPin } from 'lucide-react';
import { CONFIG } from '@/lib/config';
import { fetchETicket } from '@/lib/api';
import { RegistrationRecord } from '@/lib/whatsapp';
import { formatDateIndo } from '@/lib/utils';

function ETicketContent() {
  const searchParams = useSearchParams();
  const regId = searchParams.get('id');

  const [loading, setLoading] = useState(true);
  const [ticketData, setTicketData] = useState<RegistrationRecord | null>(null);
  const [errorMsg, setErrorMsg] = useState<string | null>(null);
  const [qrCodeUrl, setQrCodeUrl] = useState<string>('');

  useEffect(() => {
    if (!regId) {
      setErrorMsg('Parameter ID Pendaftaran tidak ditemukan pada tautan.');
      setLoading(false);
      return;
    }

    const loadData = async () => {
      setLoading(true);
      try {
        const data = await fetchETicket(regId);
        setTicketData(data);

        // Generate QR Code dari URL halaman ini
        const fullUrl = window.location.href;
        const qr = await QRCode.toDataURL(fullUrl, {
          width: 250,
          margin: 1,
          color: {
            dark: '#1a2332',
            light: '#ffffff',
          },
        });
        setQrCodeUrl(qr);
      } catch (err: any) {
        setErrorMsg(err.message || 'Gagal memuat data E-Ticket');
      } finally {
        setLoading(false);
      }
    };

    loadData();
  }, [regId]);

  const handlePrint = () => {
    window.print();
  };

  if (loading) {
    return (
      <div className="pt-40 pb-24 min-h-screen bg-neutral-100 flex flex-col items-center justify-center">
        <Loader2 className="w-10 h-10 animate-spin text-brand-500 mb-4" />
        <span className="text-sm font-semibold text-neutral-600">Memuat E-Ticket Resmi...</span>
      </div>
    );
  }

  if (errorMsg || !ticketData) {
    return (
      <div className="pt-36 pb-24 min-h-screen bg-neutral-100 flex items-center justify-center px-4">
        <div className="max-w-md w-full bg-white rounded-3xl p-8 shadow-xl text-center border border-neutral-200">
          <div className="w-14 h-14 rounded-full bg-red-100 text-red-500 flex items-center justify-center mx-auto mb-4">
            <AlertCircle className="w-8 h-8" />
          </div>
          <h2 className="font-heading font-bold text-2xl text-neutral-900 uppercase">
            E-Ticket Tidak Aktif
          </h2>
          <p className="text-xs text-neutral-500 mt-2 mb-6 leading-relaxed">
            {errorMsg}
          </p>
          <Link
            href="/"
            className="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl text-xs font-semibold bg-brand-500 text-white hover:bg-brand-600 transition-colors"
          >
            <ArrowLeft className="w-4 h-4" />
            <span>Kembali ke Beranda</span>
          </Link>
        </div>
      </div>
    );
  }

  return (
    <div className="pt-28 pb-20 min-h-screen bg-neutral-100">
      <div className="max-w-xl mx-auto px-4">
        {/* Tombol Aksi Layar (Disembunyikan saat Print) */}
        <div className="flex items-center justify-between gap-4 mb-6 print:hidden">
          <Link
            href="/"
            className="inline-flex items-center gap-2 text-xs font-semibold text-neutral-600 hover:text-brand-500 transition-colors"
          >
            <ArrowLeft className="w-4 h-4" />
            <span>Kembali ke Beranda</span>
          </Link>

          <button
            onClick={handlePrint}
            className="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-semibold bg-white border border-neutral-200 text-neutral-800 hover:bg-neutral-50 shadow-sm transition-colors"
          >
            <Printer className="w-4 h-4 text-brand-500" />
            <span>Cetak / Simpan PDF</span>
          </button>
        </div>

        {/* Card Tiket Resmi (Desain Bersih & Elegan untuk Screenshot & Cetak) */}
        <div className="bg-white rounded-3xl border border-neutral-300 shadow-xl overflow-hidden text-neutral-900 print:shadow-none print:border-neutral-400">
          {/* Header Banner E-Ticket */}
          <div className="bg-gradient-to-r from-[#1a2332] via-[#243041] to-[#1a2332] text-white p-6 sm:p-8 text-center relative overflow-hidden">
            <div className="w-12 h-12 rounded-xl bg-brand-500 flex items-center justify-center mx-auto mb-3 text-white shadow-md">
              <Target className="w-7 h-7 stroke-[2.2]" />
            </div>
            <span className="text-[10px] tracking-widest text-brand-400 uppercase font-bold block mb-1">
              E-TICKET RESMI PESERTA
            </span>
            <h1 className="font-heading font-extrabold text-2xl sm:text-3xl uppercase tracking-tight">
              BDA SHOOTING CHAMPIONSHIP 2026
            </h1>
            <p className="text-xs text-neutral-300 mt-1">
              Resimen I Pasukan Pelopor — Kedung Halang, Bogor
            </p>
          </div>

          {/* Kotak Nomor Peserta & Status Pembayaran */}
          <div className="p-6 bg-amber-50/50 border-b border-neutral-200 flex flex-col sm:flex-row items-center justify-between gap-4 text-center sm:text-left">
            <div>
              <span className="text-[11px] uppercase tracking-wider text-neutral-500 block font-semibold">
                Nomor Dada / Peserta:
              </span>
              <span className="font-heading font-extrabold text-3xl sm:text-4xl text-brand-500 tracking-wider">
                {ticketData.no_peserta || 'BSC-000'}
              </span>
            </div>

            <div className="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">
              <CheckCircle2 className="w-4 h-4 text-emerald-600" />
              <span>PEMBAYARAN LUNAS</span>
            </div>
          </div>

          {/* Rincian Identitas Peserta */}
          <div className="p-6 sm:p-8 space-y-4">
            <div className="space-y-2.5 text-xs sm:text-sm">
              <div className="flex justify-between py-2 border-b border-neutral-100">
                <span className="text-neutral-500 font-medium">Nama Lengkap</span>
                <span className="font-bold text-neutral-900 text-right">{ticketData.nama}</span>
              </div>
              <div className="flex justify-between py-2 border-b border-neutral-100">
                <span className="text-neutral-500 font-medium">Pangkat / NRP</span>
                <span className="font-semibold text-neutral-900 text-right">{ticketData.pangkat} / {ticketData.nrp}</span>
              </div>
              <div className="flex justify-between py-2 border-b border-neutral-100">
                <span className="text-neutral-500 font-medium">Satuan / Club</span>
                <span className="font-semibold text-neutral-900 text-right">{ticketData.satuan}</span>
              </div>
              <div className="flex justify-between py-2 border-b border-neutral-100">
                <span className="text-neutral-500 font-medium">Kategori Diikuti</span>
                <span className="font-bold text-brand-500 text-right">{ticketData.kategori}</span>
              </div>
              <div className="flex justify-between py-2 border-b border-neutral-100">
                <span className="text-neutral-500 font-medium">No. WhatsApp</span>
                <span className="font-mono text-neutral-800 text-right">{ticketData.telepon}</span>
              </div>
              <div className="flex justify-between py-2 border-b border-neutral-100">
                <span className="text-neutral-500 font-medium">ID Registrasi</span>
                <span className="font-mono text-neutral-500 text-right">{ticketData.registration_id}</span>
              </div>
            </div>

            {/* Foto KTA & QR Code Section */}
            <div className="grid grid-cols-2 gap-4 pt-4">
              {/* Foto KTA */}
              <div className="text-center">
                <span className="text-[11px] font-semibold uppercase tracking-wider text-neutral-500 block mb-2">
                  Foto KTA / Pengenal
                </span>
                <div className="w-full h-36 rounded-2xl border border-neutral-200 bg-neutral-50 overflow-hidden flex items-center justify-center p-1">
                  {ticketData.kta_url ? (
                    // eslint-disable-next-line @next/next/no-img-element
                    <img src={ticketData.kta_url} alt="KTA Peserta" className="max-h-full max-w-full object-contain rounded-xl" />
                  ) : (
                    <span className="text-[10px] text-neutral-400 italic">Foto KTA Tersimpan</span>
                  )}
                </div>
              </div>

              {/* QR Code */}
              <div className="text-center">
                <span className="text-[11px] font-semibold uppercase tracking-wider text-neutral-500 block mb-2">
                  QR Code Validasi
                </span>
                <div className="w-full h-36 rounded-2xl border border-neutral-200 bg-white flex items-center justify-center p-1 shadow-sm">
                  {qrCodeUrl ? (
                    // eslint-disable-next-line @next/next/no-img-element
                    <img src={qrCodeUrl} alt="QR Code E-Ticket" className="w-32 h-32 object-contain" />
                  ) : (
                    <Loader2 className="w-6 h-6 animate-spin text-neutral-400" />
                  )}
                </div>
              </div>
            </div>

            {/* Catatan Registrasi Ulang */}
            <div className="mt-6 pt-6 border-t border-neutral-200 text-center space-y-1.5">
              <p className="text-xs font-semibold text-neutral-800">
                Tunjukkan E-Ticket &amp; QR Code ini saat registrasi ulang di lokasi lapangan.
              </p>
              <div className="flex items-center justify-center gap-4 text-[11px] text-neutral-500">
                <span className="flex items-center gap-1">
                  <Calendar className="w-3 h-3 text-brand-500" />
                  <span>17 — 18 Oktober 2026</span>
                </span>
                <span className="flex items-center gap-1">
                  <MapPin className="w-3 h-3 text-brand-500" />
                  <span>Shooting House Resimen I</span>
                </span>
              </div>
            </div>
          </div>

          {/* Footer Bar E-Ticket */}
          <div className="bg-neutral-50 border-t border-neutral-200 py-3 px-6 text-center text-[10px] text-neutral-400 uppercase tracking-widest font-semibold">
            Panitia BDA 750 • Resimen I Pasukan Pelopor Kedung Halang
          </div>
        </div>
      </div>
    </div>
  );
}

export default function ETicketPage() {
  return (
    <Suspense
      fallback={
        <div className="pt-40 pb-24 min-h-screen bg-neutral-100 flex flex-col items-center justify-center">
          <Loader2 className="w-10 h-10 animate-spin text-brand-500 mb-4" />
          <span className="text-sm font-semibold text-neutral-600">Memuat E-Ticket...</span>
        </div>
      }
    >
      <ETicketContent />
    </Suspense>
  );
}
