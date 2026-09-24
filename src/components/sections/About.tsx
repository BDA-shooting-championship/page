import React from 'react';
import { Target, Users, Trophy, Shield, CheckCircle2 } from 'lucide-react';
import { CONFIG } from '@/lib/config';

export default function About() {
  const stats = [
    { label: 'Target Peserta', value: '100+', desc: 'Anggota Resimen I' },
    { label: 'Kategori Lomba', value: '2', desc: 'Presisi 20M & Dueling' },
    { label: 'Hari Pertandingan', value: '2', desc: '17 — 18 Oktober' },
    { label: 'Total Juara', value: '6', desc: 'Podium 1, 2, dan 3' },
  ];

  const highlights = [
    'Meningkatkan keterampilan dan kesiapsiagaan operasional menembak prajurit',
    'Menjaring bibit petembak andal kesatuan untuk kejuaraan tingkat nasional',
    'Mempererat silaturahmi dan solidaritas korps antar anggota Resimen I',
    'Standar keselamatan ketat dengan pengawasan Range Officer (RO) berpengalaman',
  ];

  return (
    <section className="py-20 lg:py-24">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
          {/* Sisi Kiri: Deskripsi & Tujuan */}
          <div className="lg:col-span-7 space-y-6">
            <div className="inline-flex items-center gap-2 text-xs font-semibold text-brand-600 dark:text-brand-400 uppercase tracking-widest">
              <Shield className="w-3.5 h-3.5" />
              <span>Tentang Kejuaraan</span>
            </div>

            <h2 className="font-heading font-bold text-3xl sm:text-4xl text-neutral-900 dark:text-white uppercase tracking-tight">
              Dedikasi Kemahiran Menembak Prajurit Pelopor
            </h2>

            <p className="text-neutral-600 dark:text-neutral-300 leading-relaxed">
              Menembak merupakan keterampilan mutlak yang wajib dikuasai dan senantiasa diasah oleh setiap anggota Resimen I Pasukan Pelopor Korbrimob Polri. Dalam momentum memperingati <strong>Anniversary Letting BDA 750 yang ke-7</strong>, kejuaraan ini diselenggarakan sebagai sarana evaluasi, kompetisi sehat, serta penguatan soliditas satuan.
            </p>

            <div className="grid grid-cols-1 sm:grid-cols-2 gap-3.5 pt-2">
              {highlights.map((point, i) => (
                <div key={i} className="flex items-start gap-2.5">
                  <CheckCircle2 className="w-5 h-5 text-brand-500 shrink-0 mt-0.5" />
                  <span className="text-sm text-neutral-700 dark:text-neutral-300">
                    {point}
                  </span>
                </div>
              ))}
            </div>

            <div className="p-4 rounded-xl bg-neutral-100/80 dark:bg-[#1a2332] border border-neutral-200 dark:border-neutral-800 text-xs text-neutral-600 dark:text-neutral-400">
              <strong className="text-neutral-900 dark:text-white font-semibold">Semboyan:</strong> &ldquo;MAJU — Melangkah, Aktif, Jelas, Unggul&rdquo;
            </div>
          </div>

          {/* Sisi Kanan: Stats Grid */}
          <div className="lg:col-span-5">
            <div className="grid grid-cols-2 gap-4">
              {stats.map((stat, i) => (
                <div
                  key={i}
                  className="p-6 rounded-2xl bg-white dark:bg-[#1a2332] border border-neutral-200 dark:border-neutral-800 shadow-sm flex flex-col justify-between"
                >
                  <span className="font-heading font-bold text-4xl sm:text-5xl text-brand-500">
                    {stat.value}
                  </span>
                  <div className="mt-4">
                    <span className="font-semibold text-sm text-neutral-900 dark:text-white block">
                      {stat.label}
                    </span>
                    <span className="text-xs text-neutral-500 dark:text-neutral-400">
                      {stat.desc}
                    </span>
                  </div>
                </div>
              ))}
            </div>
          </div>
        </div>
      </div>
    </section>
  );
}
