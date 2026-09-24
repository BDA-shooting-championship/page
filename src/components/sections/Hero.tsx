'use client';

import React from 'react';
import Link from 'next/link';
import { Target, Calendar, MapPin, ArrowRight, ShieldCheck, Award } from 'lucide-react';
import { CONFIG } from '@/lib/config';

export default function Hero() {
  return (
    <section id="beranda" className="relative pt-32 pb-20 lg:pt-40 lg:pb-28 overflow-hidden bg-target-pattern">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div className="max-w-3xl">
          {/* Badge Penyelenggara */}
          <div className="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-semibold bg-brand-500/10 text-brand-600 dark:text-brand-400 border border-brand-500/20 mb-6">
            <ShieldCheck className="w-4 h-4 text-brand-500" />
            <span>Anniversary Ke-7 Letting BDA 750 Resimen I Pasukan Pelopor</span>
          </div>

          {/* Judul Utama */}
          <h1 className="font-heading font-extrabold text-4xl sm:text-5xl lg:text-6xl tracking-tight text-neutral-900 dark:text-white uppercase leading-[1.08] mb-6">
            BDA SHOOTING <br />
            <span className="text-transparent bg-clip-text bg-gradient-to-r from-brand-500 via-brand-600 to-amber-600">
              CHAMPIONSHIP 2026
            </span>
          </h1>

          {/* Subtitle */}
          <p className="text-lg sm:text-xl text-neutral-600 dark:text-neutral-300 font-normal leading-relaxed mb-8 max-w-2xl">
            Ajang kompetisi menembak presisi dan ketangkasan dueling plat bergengsi bagi prajurit Resimen I Pasukan Pelopor untuk memupuk sportivitas, disiplin, dan prestasi.
          </p>

          {/* Info Singkat Tempat & Waktu */}
          <div className="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 pb-8 border-y border-neutral-200/80 dark:border-neutral-800/80 mb-8">
            <div className="flex items-center gap-3">
              <div className="w-10 h-10 rounded-lg bg-neutral-100 dark:bg-neutral-800 flex items-center justify-center text-brand-500 shrink-0">
                <Calendar className="w-5 h-5" />
              </div>
              <div>
                <span className="text-xs text-neutral-500 dark:text-neutral-400 uppercase tracking-wider font-semibold block">
                  Tanggal Kegiatan
                </span>
                <span className="text-sm font-semibold text-neutral-900 dark:text-neutral-100">
                  17 — 18 Oktober 2026
                </span>
              </div>
            </div>

            <div className="flex items-center gap-3">
              <div className="w-10 h-10 rounded-lg bg-neutral-100 dark:bg-neutral-800 flex items-center justify-center text-brand-500 shrink-0">
                <MapPin className="w-5 h-5" />
              </div>
              <div>
                <span className="text-xs text-neutral-500 dark:text-neutral-400 uppercase tracking-wider font-semibold block">
                  Lokasi Lapangan
                </span>
                <span className="text-sm font-semibold text-neutral-900 dark:text-neutral-100">
                  Resimen I Pasukan Pelopor, Bogor
                </span>
              </div>
            </div>
          </div>

          {/* Tombol Aksi */}
          <div className="flex flex-wrap items-center gap-4">
            <Link
              href="/daftar"
              className="inline-flex items-center gap-2.5 px-6 py-3.5 rounded-xl font-semibold text-sm bg-brand-500 hover:bg-brand-600 text-white shadow-lg shadow-brand-500/25 hover:shadow-brand-500/40 hover:-translate-y-0.5 transition-all"
            >
              <Target className="w-5 h-5" />
              <span>Daftar Sekarang</span>
              <ArrowRight className="w-4 h-4 ml-1" />
            </Link>

            <a
              href="#aturan"
              className="inline-flex items-center gap-2 px-6 py-3.5 rounded-xl font-semibold text-sm bg-white dark:bg-[#1a2332] text-neutral-800 dark:text-neutral-200 border border-neutral-200 dark:border-neutral-800 hover:bg-neutral-50 dark:hover:bg-neutral-800/80 transition-all"
            >
              <Award className="w-4 h-4 text-brand-500" />
              <span>Petunjuk Teknis (Juknis)</span>
            </a>
          </div>
        </div>
      </div>
    </section>
  );
}
