'use client';

import React, { useState } from 'react';
import { Phone, MessageCircle, CreditCard, Copy, Check, ShieldCheck } from 'lucide-react';
import { CONFIG } from '@/lib/config';
import { normalizePhoneForWA } from '@/lib/utils';

export default function Contact() {
  const [copied, setCopied] = useState(false);

  const handleCopyAccount = () => {
    navigator.clipboard.writeText(CONFIG.BANK_ACCOUNT.accountNumber);
    setCopied(true);
    setTimeout(() => setCopied(false), 2500);
  };

  return (
    <section id="kontak" className="py-20 lg:py-28 bg-neutral-100/60 dark:bg-[#131922]/60 border-t border-neutral-200/80 dark:border-neutral-800/80">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="text-center max-w-3xl mx-auto mb-16">
          <div className="inline-flex items-center gap-2 text-xs font-semibold text-brand-600 dark:text-brand-400 uppercase tracking-widest mb-3">
            <Phone className="w-3.5 h-3.5" />
            <span>Pusat Informasi</span>
          </div>
          <h2 className="font-heading font-bold text-3xl sm:text-4xl text-neutral-900 dark:text-white uppercase tracking-tight">
            Narahubung &amp; Rekening Resmi
          </h2>
          <p className="text-neutral-600 dark:text-neutral-400 text-sm sm:text-base mt-2">
            Hubungi panitia pelaksana untuk koordinasi teknis, pendaftaran, atau konfirmasi bukti pembayaran.
          </p>
        </div>

        <div className="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
          {/* Sisi Kiri: Rekening Resmi */}
          <div className="lg:col-span-5 bg-white dark:bg-[#1a2332] rounded-2xl border border-neutral-200 dark:border-neutral-800 p-8 shadow-sm">
            <div className="flex items-center gap-3 mb-6">
              <div className="w-12 h-12 rounded-xl bg-brand-500/10 text-brand-500 flex items-center justify-center">
                <CreditCard className="w-6 h-6 stroke-[2.2]" />
              </div>
              <div>
                <span className="text-xs uppercase font-semibold text-brand-600 dark:text-brand-400 block">
                  Rekening Pendaftaran
                </span>
                <h3 className="font-heading font-bold text-xl text-neutral-900 dark:text-white uppercase">
                  Bank {CONFIG.BANK_ACCOUNT.bank}
                </h3>
              </div>
            </div>

            <div className="p-5 rounded-xl bg-neutral-50 dark:bg-[#151c27] border border-neutral-200 dark:border-neutral-800/80 mb-6">
              <span className="text-xs text-neutral-500 dark:text-neutral-400 block mb-1 font-medium">
                Nomor Rekening:
              </span>
              <div className="flex items-center justify-between gap-2">
                <span className="font-mono text-xl sm:text-2xl font-bold text-neutral-900 dark:text-white tracking-wider">
                  {CONFIG.BANK_ACCOUNT.accountNumber}
                </span>
                <button
                  onClick={handleCopyAccount}
                  aria-label="Salin Nomor Rekening"
                  className="p-2 rounded-lg bg-white dark:bg-[#1e293b] border border-neutral-200 dark:border-neutral-700 text-neutral-700 dark:text-neutral-200 hover:text-brand-500 transition-colors shrink-0"
                >
                  {copied ? <Check className="w-5 h-5 text-emerald-500" /> : <Copy className="w-5 h-5" />}
                </button>
              </div>
              <span className="text-xs text-neutral-600 dark:text-neutral-400 block mt-2">
                Atas Nama: <strong className="text-neutral-900 dark:text-neutral-200">{CONFIG.BANK_ACCOUNT.accountName}</strong>
              </span>
            </div>

            <div className="space-y-2 text-xs text-neutral-500 dark:text-neutral-400">
              <p className="flex items-center gap-2">
                <ShieldCheck className="w-4 h-4 text-brand-500 shrink-0" />
                <span>Simpan bukti transfer saat melakukan pembayaran untuk diunggah di formulir.</span>
              </p>
              <p className="flex items-center gap-2">
                <ShieldCheck className="w-4 h-4 text-brand-500 shrink-0" />
                <span>Biaya pendaftaran: Rp 200.000 / kategori (Umum POLRI) dan Rp 100.000 (Khusus BDA).</span>
              </p>
            </div>
          </div>

          {/* Sisi Kanan: Kontak Panitia */}
          <div className="lg:col-span-7 grid grid-cols-1 sm:grid-cols-2 gap-4">
            {CONFIG.CONTACTS.map((c, idx) => (
              <div
                key={idx}
                className="p-6 rounded-2xl bg-white dark:bg-[#1a2332] border border-neutral-200 dark:border-neutral-800 shadow-sm flex flex-col justify-between hover:border-brand-500/50 transition-colors"
              >
                <div>
                  <span className="text-[11px] font-semibold uppercase tracking-wider text-brand-600 dark:text-brand-400 bg-brand-500/10 px-2.5 py-0.5 rounded-full inline-block mb-3">
                    {c.role}
                  </span>
                  <h4 className="font-heading font-bold text-lg text-neutral-900 dark:text-white uppercase">
                    {c.name}
                  </h4>
                  <p className="text-sm font-mono text-neutral-500 dark:text-neutral-400 mt-1">
                    {c.displayPhone}
                  </p>
                </div>

                <div className="mt-6 pt-4 border-t border-neutral-100 dark:border-neutral-800/80">
                  <a
                    href={`https://wa.me/${normalizePhoneForWA(c.phone)}?text=${encodeURIComponent(
                      `Halo Panitia BDA Shooting Championship 2026, saya ingin menanyakan perihal kegiatan...`
                    )}`}
                    target="_blank"
                    rel="noopener noreferrer"
                    className="inline-flex items-center justify-center gap-2 w-full py-2.5 rounded-xl text-xs font-semibold bg-emerald-600 hover:bg-emerald-700 text-white shadow-sm transition-colors"
                  >
                    <MessageCircle className="w-4 h-4" />
                    <span>Hubungi via WhatsApp</span>
                  </a>
                </div>
              </div>
            ))}
          </div>
        </div>
      </div>
    </section>
  );
}
