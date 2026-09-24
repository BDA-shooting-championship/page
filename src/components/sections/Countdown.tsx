'use client';

import React, { useState, useEffect } from 'react';
import { CONFIG } from '@/lib/config';
import { Clock } from 'lucide-react';

interface TimeLeft {
  days: number;
  hours: number;
  minutes: number;
  seconds: number;
}

export default function Countdown() {
  const [timeLeft, setTimeLeft] = useState<TimeLeft>({ days: 0, hours: 0, minutes: 0, seconds: 0 });
  const [isMounted, setIsMounted] = useState(false);

  useEffect(() => {
    setIsMounted(true);
    const targetDate = new Date(CONFIG.EVENT_DATE_START).getTime();

    const updateCountdown = () => {
      const now = new Date().getTime();
      const distance = targetDate - now;

      if (distance <= 0) {
        setTimeLeft({ days: 0, hours: 0, minutes: 0, seconds: 0 });
        return;
      }

      setTimeLeft({
        days: Math.floor(distance / (1000 * 60 * 60 * 24)),
        hours: Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60)),
        minutes: Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60)),
        seconds: Math.floor((distance % (1000 * 60)) / 1000),
      });
    };

    updateCountdown();
    const interval = setInterval(updateCountdown, 1000);
    return () => clearInterval(interval);
  }, []);

  const timeUnits = [
    { label: 'Hari', value: isMounted ? String(timeLeft.days).padStart(2, '0') : '--' },
    { label: 'Jam', value: isMounted ? String(timeLeft.hours).padStart(2, '0') : '--' },
    { label: 'Menit', value: isMounted ? String(timeLeft.minutes).padStart(2, '0') : '--' },
    { label: 'Detik', value: isMounted ? String(timeLeft.seconds).padStart(2, '0') : '--' },
  ];

  return (
    <section className="py-12 bg-white/60 dark:bg-[#131922]/60 border-y border-neutral-200/80 dark:border-neutral-800/80 backdrop-blur-sm">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="flex flex-col lg:flex-row items-center justify-between gap-8">
          <div className="text-center lg:text-left">
            <div className="inline-flex items-center gap-2 text-xs font-semibold text-brand-600 dark:text-brand-400 uppercase tracking-widest mb-1.5">
              <Clock className="w-3.5 h-3.5" />
              <span>Hitung Mundur Pelaksanaan</span>
            </div>
            <h3 className="font-heading font-bold text-2xl text-neutral-900 dark:text-white uppercase tracking-tight">
              Menuju Hari Pertandingan
            </h3>
            <p className="text-sm text-neutral-500 dark:text-neutral-400 mt-0.5">
              Sabtu, 17 Oktober 2026 di Lapangan Tembak Shooting House
            </p>
          </div>

          {/* Time Units Grid */}
          <div className="grid grid-cols-4 gap-3 sm:gap-5 w-full sm:w-auto">
            {timeUnits.map((item, idx) => (
              <div
                key={idx}
                className="flex flex-col items-center justify-center p-3.5 sm:px-6 sm:py-4 rounded-xl bg-white dark:bg-[#1a2332] border border-neutral-200 dark:border-neutral-800 shadow-sm min-w-[70px] sm:min-w-[95px]"
              >
                <span className="font-heading font-bold text-2xl sm:text-4xl text-brand-500 tracking-tight">
                  {item.value}
                </span>
                <span className="text-[10px] sm:text-xs font-semibold uppercase tracking-wider text-neutral-500 dark:text-neutral-400 mt-1">
                  {item.label}
                </span>
              </div>
            ))}
          </div>
        </div>
      </div>
    </section>
  );
}
