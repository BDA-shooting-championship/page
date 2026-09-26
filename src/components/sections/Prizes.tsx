import React from 'react';
import { Trophy, Medal, Award, Sparkles } from 'lucide-react';
import { formatRupiah } from '@/lib/utils';

export default function Prizes() {
  const prizeTiers = [
    {
      place: 2,
      title: 'Juara 2',
      category: 'Presisi 20M & Dueling Plat',
      reward: 3000000,
      icon: Medal,
      color: 'from-slate-400 to-slate-600',
      border: 'border-slate-300 dark:border-slate-700',
      badge: 'Silver Medalist',
      perks: ['Trophy Juara 2', 'Sertifikat Penghargaan', 'Piagam & Banner Juara'],
      height: 'lg:mt-8',
    },
    {
      place: 1,
      title: 'Juara 1',
      category: 'Presisi 20M & Dueling Plat',
      reward: 3000000,
      icon: Trophy,
      color: 'from-amber-400 to-amber-600',
      border: 'border-amber-400/80 dark:border-amber-500/80 ring-2 ring-amber-400/20',
      badge: 'Utama / Champion',
      perks: ['Trophy Juara 1 Bergengsi', 'Sertifikat Penghargaan Resmi', 'Piagam & Banner Juara 1', 'Pengakuan Juara Tingkat Kesatuan'],
      height: 'lg:mt-0',
      featured: true,
    },
    {
      place: 3,
      title: 'Juara 3',
      category: 'Presisi 20M & Dueling Plat',
      reward: 3000000,
      icon: Award,
      color: 'from-amber-700 to-amber-900',
      border: 'border-amber-700/40 dark:border-amber-800/40',
      badge: 'Bronze Medalist',
      perks: ['Trophy Juara 3', 'Sertifikat Penghargaan', 'Piagam & Banner Juara'],
      height: 'lg:mt-12',
    },
  ];

  return (
    <section id="hadiah" className="py-20 lg:py-28">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="text-center max-w-3xl mx-auto mb-16">
          <div className="inline-flex items-center gap-2 text-xs font-semibold text-brand-600 dark:text-brand-400 uppercase tracking-widest mb-3">
            <Trophy className="w-3.5 h-3.5" />
            <span>Penghargaan & Apresiasi</span>
          </div>
          <h2 className="font-heading font-bold text-3xl sm:text-4xl text-neutral-900 dark:text-white uppercase tracking-tight">
            Hadiah Pemenang Kejuaraan
          </h2>
          <p className="text-neutral-600 dark:text-neutral-400 text-sm sm:text-base mt-2">
            Apresiasi dan uang tunai berlaku untuk masing-masing kategori lomba (Pistol Presisi 20M dan Dueling Plat).
          </p>
        </div>

        {/* Podium Layout */}
        <div className="grid grid-cols-1 md:grid-cols-3 gap-6 lg:gap-8 items-end max-w-5xl mx-auto">
          {prizeTiers.map((tier) => {
            const Icon = tier.icon;
            return (
              <div
                key={tier.place}
                className={`relative rounded-2xl bg-white dark:bg-[#1a2332] border ${tier.border} shadow-lg p-6 sm:p-8 flex flex-col justify-between transition-transform duration-300 hover:-translate-y-1 ${tier.height}`}
              >
                {tier.featured && (
                  <div className="absolute -top-3.5 left-1/2 -translate-x-1/2 px-4 py-1 rounded-full bg-gradient-to-r from-amber-500 to-amber-600 text-white text-[11px] font-bold uppercase tracking-wider shadow-md flex items-center gap-1.5 whitespace-nowrap">
                    <Sparkles className="w-3.5 h-3.5" />
                    <span>Podium Tertinggi</span>
                  </div>
                )}

                <div>
                  <div className="flex items-center justify-between mb-4">
                    <span className="text-xs font-semibold tracking-wider uppercase text-neutral-500 dark:text-neutral-400">
                      {tier.badge}
                    </span>
                    <div className={`w-12 h-12 rounded-xl bg-gradient-to-br ${tier.color} text-white flex items-center justify-center shadow-md`}>
                      <Icon className="w-6 h-6 stroke-[2.2]" />
                    </div>
                  </div>

                  <h3 className="font-heading font-bold text-2xl text-neutral-900 dark:text-white uppercase tracking-tight">
                    {tier.title}
                  </h3>
                  <p className="text-xs text-neutral-500 dark:text-neutral-400 mb-6">
                    {tier.category}
                  </p>

                  <div className="py-4 border-y border-neutral-100 dark:border-neutral-800/80 mb-6">
                    <span className="text-[11px] font-semibold uppercase tracking-wider text-neutral-500 dark:text-neutral-400 block">
                      Uang Tunai
                    </span>
                    <span className="font-heading font-bold text-3xl sm:text-4xl text-brand-500">
                      {formatRupiah(tier.reward)}
                    </span>
                  </div>

                  <ul className="space-y-2.5 text-xs text-neutral-600 dark:text-neutral-300">
                    {tier.perks.map((perk, idx) => (
                      <li key={idx} className="flex items-center gap-2">
                        <span className="w-1.5 h-1.5 rounded-full bg-brand-500 shrink-0" />
                        <span>{perk}</span>
                      </li>
                    ))}
                  </ul>
                </div>
              </div>
            );
          })}
        </div>
      </div>
    </section>
  );
}
