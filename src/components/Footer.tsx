import React from 'react';
import Link from 'next/link';
import { Target, Shield, MapPin, Calendar, Award } from 'lucide-react';
import { CONFIG } from '@/lib/config';

export default function Footer() {
  return (
    <footer className="bg-white dark:bg-[#0b0e14] border-t border-neutral-200 dark:border-neutral-800/80 transition-colors">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16">
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 lg:gap-12">
          {/* Col 1: Brand & Penyelenggara */}
          <div className="space-y-4">
            <div className="flex items-center gap-3">
              <div className="w-9 h-9 rounded-xl bg-brand-500 flex items-center justify-center text-white">
                <Target className="w-5 h-5 stroke-[2.2]" />
              </div>
              <span className="font-heading font-bold text-lg uppercase tracking-wider text-neutral-900 dark:text-white">
                BDA <span className="text-brand-500">2026</span>
              </span>
            </div>
            <p className="text-sm text-neutral-600 dark:text-neutral-400 leading-relaxed">
              Kejuaraan Menembak Pistol Presisi 20M & Dueling Plat dalam rangka memperingati Anniversary Letting BDA 750 ke-7.
            </p>
            <div className="text-xs text-neutral-500 dark:text-neutral-500">
              <span className="font-semibold block text-neutral-700 dark:text-neutral-300">Penyelenggara:</span>
              {CONFIG.ORGANIZER} — {CONFIG.UNIT}
            </div>
          </div>

          {/* Col 2: Navigasi Cepat */}
          <div>
            <h4 className="font-heading font-semibold text-sm tracking-wider uppercase text-neutral-900 dark:text-white mb-4">
              Navigasi
            </h4>
            <ul className="space-y-2.5 text-sm text-neutral-600 dark:text-neutral-400">
              <li>
                <a href="/#beranda" className="hover:text-brand-500 transition-colors">Beranda</a>
              </li>
              <li>
                <a href="/#kategori" className="hover:text-brand-500 transition-colors">Kategori Pertandingan</a>
              </li>
              <li>
                <a href="/#hadiah" className="hover:text-brand-500 transition-colors">Hadiah & Juara</a>
              </li>
              <li>
                <a href="/#jadwal" className="hover:text-brand-500 transition-colors">Jadwal Pertandingan</a>
              </li>
              <li>
                <a href="/#aturan" className="hover:text-brand-500 transition-colors">Aturan & Juknis</a>
              </li>
            </ul>
          </div>

          {/* Col 3: Lokasi & Waktu */}
          <div>
            <h4 className="font-heading font-semibold text-sm tracking-wider uppercase text-neutral-900 dark:text-white mb-4">
              Waktu & Tempat
            </h4>
            <div className="space-y-3 text-sm text-neutral-600 dark:text-neutral-400">
              <div className="flex items-start gap-2.5">
                <Calendar className="w-4 h-4 text-brand-500 shrink-0 mt-0.5" />
                <span>17 — 18 Oktober 2026<br />(Technical Meeting: 16 Oktober)</span>
              </div>
              <div className="flex items-start gap-2.5">
                <MapPin className="w-4 h-4 text-brand-500 shrink-0 mt-0.5" />
                <span>Lapangan Tembak Shooting House Resimen I Pasukan Pelopor, Kedung Halang, Bogor</span>
              </div>
            </div>
          </div>

          {/* Col 4: Informasi Pendaftaran */}
          <div>
            <h4 className="font-heading font-semibold text-sm tracking-wider uppercase text-neutral-900 dark:text-white mb-4">
              Pendaftaran & Admin
            </h4>
            <p className="text-sm text-neutral-600 dark:text-neutral-400 mb-4">
              Pendaftaran terbuka untuk anggota Resimen I Pasukan Pelopor.
            </p>
            <div className="flex flex-col gap-2.5">
              <Link
                href="/daftar"
                className="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg text-xs font-semibold bg-brand-500 text-white hover:bg-brand-600 transition-colors"
              >
                Formulir Pendaftaran
              </Link>
              <Link
                href="/admin"
                className="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg text-xs font-semibold border border-neutral-300 dark:border-neutral-700 text-neutral-700 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800 transition-colors"
              >
                Portal Admin Panitia
              </Link>
            </div>
          </div>
        </div>

        <div className="mt-12 pt-8 border-t border-neutral-200 dark:border-neutral-800/80 flex flex-col sm:flex-row items-center justify-between text-xs text-neutral-500 gap-4">
          <p>© 2026 BDA Shooting Championship. Seluruh hak cipta dilindungi.</p>
          <p className="flex items-center gap-1.5">
            <Shield className="w-3.5 h-3.5 text-brand-500" />
            <span>Resimen I Pasukan Pelopor — Kedung Halang, Bogor</span>
          </p>
        </div>
      </div>
    </footer>
  );
}
