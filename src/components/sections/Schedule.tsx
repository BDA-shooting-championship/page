import React from 'react';
import { Calendar, Clock, MapPin, CheckCircle } from 'lucide-react';

export default function Schedule() {
  const scheduleData = [
    {
      day: 'Jum’at',
      date: '16 Oktober 2026',
      badge: 'Persiapan & Pengarahan',
      events: [
        {
          time: '08:00 — 10:00 WIB',
          title: 'Technical Meeting (TM) Seluruh Peserta',
          desc: 'Penjelasan tata tertib pertandingan, undian gelombang (heat), dan pengecekan senjata.',
        },
        {
          time: '10:30 — 16:00 WIB',
          title: 'Latihan Resmi (Official Practice)',
          desc: 'Uji coba lintasan tembak presisi 20M dan penyesuaian lapangan dueling plat.',
        },
      ],
    },
    {
      day: 'Sabtu',
      date: '17 Oktober 2026',
      badge: 'Hari Pertandingan I',
      events: [
        {
          time: '07:30 — 08:30 WIB',
          title: 'Upacara Pembukaan & Apel Petembak',
          desc: 'Pengarahan Komandan Resimen I Pasukan Pelopor selaku Pelindung Kegiatan.',
        },
        {
          time: '08:30 — 16:30 WIB',
          title: 'Pertandingan Pistol Presisi 20 Meter',
          desc: 'Pelaksanaan seluruh gelombang presisi (13 butir amunisi: 3 percobaan + 10 slow fire). Rekapitulasi nilai dan penetapan juara.',
        },
      ],
    },
    {
      day: 'Minggu',
      date: '18 Oktober 2026',
      badge: 'Hari Pertandingan II & Final',
      events: [
        {
          time: '08:00 — 14:00 WIB',
          title: 'Pertandingan Dueling Plat (Sistem Gugur)',
          desc: 'Head to head dueling plat hingga perempat final, semifinal, dan perebutan medali.',
        },
        {
          time: '14:30 — 16:30 WIB',
          title: 'Babak Final & Upacara Penutupan',
          desc: 'Pengumuman pemenang, penyerahan medali, trophy, dan uang pembinaan.',
        },
      ],
    },
  ];

  return (
    <section id="jadwal" className="py-20 lg:py-28 bg-neutral-100/60 dark:bg-[#131922]/60 border-y border-neutral-200/80 dark:border-neutral-800/80">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="text-center max-w-3xl mx-auto mb-16">
          <div className="inline-flex items-center gap-2 text-xs font-semibold text-brand-600 dark:text-brand-400 uppercase tracking-widest mb-3">
            <Calendar className="w-3.5 h-3.5" />
            <span>Rangkaian Waktu</span>
          </div>
          <h2 className="font-heading font-bold text-3xl sm:text-4xl text-neutral-900 dark:text-white uppercase tracking-tight">
            Jadwal Lengkap Kegiatan
          </h2>
          <p className="text-neutral-600 dark:text-neutral-400 text-sm sm:text-base mt-2">
            Diselenggarakan di Lapangan Tembak Shooting House Resimen I Pasukan Pelopor.
          </p>
        </div>

        {/* Timeline Grid */}
        <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
          {scheduleData.map((item, idx) => (
            <div
              key={idx}
              className="bg-white dark:bg-[#1a2332] rounded-2xl border border-neutral-200 dark:border-neutral-800 shadow-sm p-6 sm:p-8 flex flex-col justify-between"
            >
              <div>
                <div className="flex items-center justify-between gap-2 mb-4">
                  <span className="text-xs font-semibold uppercase tracking-wider text-brand-600 dark:text-brand-400 bg-brand-500/10 px-3 py-1 rounded-full">
                    {item.badge}
                  </span>
                  <div className="flex items-center gap-1 text-xs text-neutral-500 dark:text-neutral-400">
                    <MapPin className="w-3.5 h-3.5 text-brand-500" />
                    <span>Lap. Shooting House</span>
                  </div>
                </div>

                <h3 className="font-heading font-bold text-2xl text-neutral-900 dark:text-white uppercase tracking-tight">
                  {item.day}
                </h3>
                <p className="text-xs font-medium text-neutral-500 dark:text-neutral-400 mb-6">
                  {item.date}
                </p>

                <div className="space-y-6 relative before:absolute before:top-2 before:bottom-2 before:left-[11px] before:w-[2px] before:bg-neutral-200 dark:before:bg-neutral-800">
                  {item.events.map((ev, evIdx) => (
                    <div key={evIdx} className="relative pl-8">
                      <div className="absolute left-1.5 top-1.5 w-3 h-3 rounded-full bg-brand-500 ring-4 ring-white dark:ring-[#1a2332]" />
                      <div className="inline-flex items-center gap-1.5 text-xs font-semibold text-brand-600 dark:text-brand-400 mb-1">
                        <Clock className="w-3.5 h-3.5" />
                        <span>{ev.time}</span>
                      </div>
                      <h4 className="font-semibold text-sm text-neutral-900 dark:text-neutral-100">
                        {ev.title}
                      </h4>
                      <p className="text-xs text-neutral-500 dark:text-neutral-400 mt-1 leading-relaxed">
                        {ev.desc}
                      </p>
                    </div>
                  ))}
                </div>
              </div>
            </div>
          ))}
        </div>
      </div>
    </section>
  );
}
