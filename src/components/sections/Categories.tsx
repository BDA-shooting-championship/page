import React from 'react';
import Link from 'next/link';
import { Target, Zap, Clock, Disc, Shield, ArrowRight } from 'lucide-react';
import { formatRupiah } from '@/lib/utils';
import { CONFIG } from '@/lib/config';

export default function Categories() {
  const categories = [
    {
      id: 'presisi',
      title: 'Pistol Presisi 20 Meter',
      subtitle: 'Kelas Individu',
      icon: Target,
      badge: 'Akurasi & Konsentrasi',
      specs: [
        { label: 'Jarak Tembak', value: '20 Meter' },
        { label: 'Sikap Menembak', value: 'Berdiri, 2 Tangan' },
        { label: 'Sasaran Target', value: '1 Lesan Ring Pistol (Nilai 1-10 + Center X)' },
        { label: 'Amunisi', value: '13 Butir (3 Percobaan + 10 Slow Fire)' },
        { label: 'Batas Waktu', value: 'Percobaan 1 Menit, Slow Fire 3 Menit' },
        { label: 'Sistem Penilaian', value: 'Akumulasi Poin Ring (Tie-breaker jumlah X)' },
        { label: 'Senjata', value: 'Pistol Dinas Organik Kaliber 9x19mm' },
      ],
      fee: CONFIG.REGISTRATION_FEE_PER_CATEGORY,
    },
    {
      id: 'dueling',
      title: 'Dueling Plat Speed',
      subtitle: 'Kelas Individu',
      icon: Zap,
      badge: 'Kecepatan & Ketangkasan',
      specs: [
        { label: 'Jarak Tembak', value: '15 Meter' },
        { label: 'Sikap Menembak', value: 'Berdiri (Lari 10m ke meja petembak)' },
        { label: 'Sasaran Target', value: '5 Plat Bulat + 1 Popper Penentu' },
        { label: 'Amunisi', value: '10 Butir per pertandingan' },
        { label: 'Batas Waktu', value: 'Tidak Terbatas (Kecepatan Waktu Gugur)' },
        { label: 'Sistem Penilaian', value: 'Sistem Gugur (Head to Head)' },
        { label: 'Aturan Khusus', value: 'Wajib jatuhkan 5 plat sebelum tembak Popper' },
      ],
      fee: CONFIG.REGISTRATION_FEE_PER_CATEGORY,
    },
  ];

  return (
    <section id="kategori" className="py-20 lg:py-28 bg-neutral-100/60 dark:bg-[#131922]/60 border-y border-neutral-200/80 dark:border-neutral-800/80">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="text-center max-w-3xl mx-auto mb-16">
          <div className="inline-flex items-center gap-2 text-xs font-semibold text-brand-600 dark:text-brand-400 uppercase tracking-widest mb-3">
            <Disc className="w-3.5 h-3.5" />
            <span>Materi Pertandingan</span>
          </div>
          <h2 className="font-heading font-bold text-3xl sm:text-4xl text-neutral-900 dark:text-white uppercase tracking-tight">
            2 Kategori Utama Kejuaraan
          </h2>
          <p className="text-neutral-600 dark:text-neutral-400 text-sm sm:text-base mt-2">
            Peserta dapat mendaftar pada salah satu kategori atau mengikuti keduanya sekaligus.
          </p>
        </div>

        {/* Categories Grid */}
        <div className="grid grid-cols-1 lg:grid-cols-2 gap-8">
          {categories.map((cat) => {
            const Icon = cat.icon;
            return (
              <div
                key={cat.id}
                className="bg-white dark:bg-[#1a2332] rounded-2xl border border-neutral-200 dark:border-neutral-800 shadow-sm overflow-hidden flex flex-col justify-between hover:border-brand-500/50 dark:hover:border-brand-500/50 transition-all group"
              >
                <div className="p-8">
                  {/* Header Card */}
                  <div className="flex items-start justify-between gap-4 mb-6">
                    <div>
                      <span className="text-xs font-semibold uppercase tracking-wider text-brand-600 dark:text-brand-400 bg-brand-500/10 px-3 py-1 rounded-full">
                        {cat.badge}
                      </span>
                      <h3 className="font-heading font-bold text-2xl sm:text-3xl text-neutral-900 dark:text-white uppercase tracking-tight mt-3">
                        {cat.title}
                      </h3>
                      <p className="text-xs font-medium text-neutral-500 dark:text-neutral-400">
                        {cat.subtitle}
                      </p>
                    </div>

                    <div className="w-14 h-14 rounded-2xl bg-brand-500/10 text-brand-500 flex items-center justify-center shrink-0 group-hover:bg-brand-500 group-hover:text-white transition-colors">
                      <Icon className="w-7 h-7 stroke-[2.2]" />
                    </div>
                  </div>

                  {/* Spesifikasi Teknis */}
                  <div className="space-y-3.5 py-4 border-t border-b border-neutral-100 dark:border-neutral-800/80">
                    {cat.specs.map((item, idx) => (
                      <div key={idx} className="flex justify-between items-start text-xs sm:text-sm gap-4">
                        <span className="text-neutral-500 dark:text-neutral-400 font-medium">
                          {item.label}
                        </span>
                        <span className="text-neutral-900 dark:text-neutral-200 font-semibold text-right">
                          {item.value}
                        </span>
                      </div>
                    ))}
                  </div>
                </div>

                {/* Footer Card with Fee and CTA */}
                <div className="px-8 py-5 bg-neutral-50 dark:bg-[#161e2b] border-t border-neutral-100 dark:border-neutral-800/80 flex items-center justify-between">
                  <div>
                    <span className="text-[11px] uppercase tracking-wider text-neutral-500 dark:text-neutral-400 block font-semibold">
                      Biaya Pendaftaran
                    </span>
                    <span className="font-heading font-bold text-xl sm:text-2xl text-brand-500">
                      {formatRupiah(cat.fee)}
                    </span>
                  </div>

                  <Link
                    href="/daftar"
                    className="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs sm:text-sm font-semibold bg-brand-500 hover:bg-brand-600 text-white shadow-sm hover:shadow transition-all"
                  >
                    <span>Pilih Kategori Ini</span>
                    <ArrowRight className="w-4 h-4" />
                  </Link>
                </div>
              </div>
            );
          })}
        </div>
      </div>
    </section>
  );
}
