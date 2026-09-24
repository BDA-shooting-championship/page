'use client';

import React, { useState } from 'react';
import { ShieldAlert, ChevronDown, CheckCircle, AlertTriangle, XCircle, AlertCircle } from 'lucide-react';

export default function Rules() {
  const [openIndex, setOpenIndex] = useState<number | null>(0);

  const toggleAccordion = (idx: number) => {
    setOpenIndex(openIndex === idx ? null : idx);
  };

  const rulesList = [
    {
      title: 'Pakaian & Seragam Peserta',
      icon: CheckCircle,
      content: (
        <ul className="space-y-2 text-sm text-neutral-600 dark:text-neutral-300">
          <li className="flex items-start gap-2">
            <span className="w-1.5 h-1.5 rounded-full bg-brand-500 mt-2 shrink-0" />
            <span>Peserta dapat mengenakan <strong>Jersey Menembak</strong>, <strong>Baju Tactical</strong>, ataupun <strong>Pakaian Dinas</strong> masing-masing.</span>
          </li>
          <li className="flex items-start gap-2">
            <span className="w-1.5 h-1.5 rounded-full bg-brand-500 mt-2 shrink-0" />
            <span><strong>Wajib mengenakan sepatu</strong> (sepatu olahraga, tactical, atau dinas). <em>Sangat dilarang menggunakan sandal</em>.</span>
          </li>
          <li className="flex items-start gap-2">
            <span className="w-1.5 h-1.5 rounded-full bg-brand-500 mt-2 shrink-0" />
            <span>Diperbolehkan menggunakan tutup kepala (topi) dan sarung tangan menembak (tidak wajib).</span>
          </li>
        </ul>
      ),
    },
    {
      title: 'Spesifikasi Senjata & Amunisi',
      icon: CheckCircle,
      content: (
        <ul className="space-y-2 text-sm text-neutral-600 dark:text-neutral-300">
          <li className="flex items-start gap-2">
            <span className="w-1.5 h-1.5 rounded-full bg-brand-500 mt-2 shrink-0" />
            <span>Menggunakan <strong>Pistol Dinas Organik</strong> masing-masing satuan (laras maksimum 5 inchi).</span>
          </li>
          <li className="flex items-start gap-2">
            <span className="w-1.5 h-1.5 rounded-full bg-brand-500 mt-2 shrink-0" />
            <span>Menggunakan amunisi standar kaliber <strong>9 x 19 mm</strong>.</span>
          </li>
          <li className="flex items-start gap-2">
            <span className="w-1.5 h-1.5 rounded-full bg-brand-500 mt-2 shrink-0" />
            <span>Menggunakan alat bidik standar <strong>Non-Optic (Pisir Pejera Logam)</strong>. Alat bidik optik (red dot) tidak diperkenankan.</span>
          </li>
        </ul>
      ),
    },
    {
      title: 'Perlengkapan Keamanan di Garis Tembak',
      icon: AlertCircle,
      content: (
        <ul className="space-y-2 text-sm text-neutral-600 dark:text-neutral-300">
          <li className="flex items-start gap-2">
            <span className="w-1.5 h-1.5 rounded-full bg-brand-500 mt-2 shrink-0" />
            <span><strong>Wajib mengenakan Kacamata Pelindung</strong> dan <strong>Pelindung Telinga (Ear Plug / Ear Muff)</strong> selama berada di garis penembakan.</span>
          </li>
          <li className="flex items-start gap-2">
            <span className="w-1.5 h-1.5 rounded-full bg-brand-500 mt-2 shrink-0" />
            <span>Peserta dapat membawa perlengkapan sendiri atau menggunakan yang disediakan panitia di garis tembak.</span>
          </li>
          <li className="flex items-start gap-2">
            <span className="w-1.5 h-1.5 rounded-full bg-brand-500 mt-2 shrink-0" />
            <span>Diperbolehkan membawa teropong monokular/binokular untuk melihat hasil perkenaan pada kategori presisi.</span>
          </li>
        </ul>
      ),
    },
    {
      title: 'Aturan Keselamatan Mutlak (Safety Rules)',
      icon: AlertTriangle,
      content: (
        <ul className="space-y-2 text-sm text-neutral-600 dark:text-neutral-300">
          <li className="flex items-start gap-2">
            <span className="w-1.5 h-1.5 rounded-full bg-amber-500 mt-2 shrink-0" />
            <span><strong>Dilarang mengeluarkan atau membidikkan senjata</strong> di luar garis tembak, kecuali di area yang telah ditetapkan sebagai <em>Safety Area</em>.</span>
          </li>
          <li className="flex items-start gap-2">
            <span className="w-1.5 h-1.5 rounded-full bg-amber-500 mt-2 shrink-0" />
            <span>Senjata hanya boleh dikeluarkan atas perintah dan instruksi langsung dari <strong>Range Officer (RO)</strong>.</span>
          </li>
          <li className="flex items-start gap-2">
            <span className="w-1.5 h-1.5 rounded-full bg-amber-500 mt-2 shrink-0" />
            <span><strong>Sudut Laras 60 Derajat:</strong> Arah laras senjata tidak boleh melebihi sudut 60° (kiri, kanan, atas, maupun bawah) dari arah sasaran tembak.</span>
          </li>
          <li className="flex items-start gap-2">
            <span className="w-1.5 h-1.5 rounded-full bg-amber-500 mt-2 shrink-0" />
            <span>Wajib mengosongkan senjata dan memperlihatkan kamar amunisi ke arah aman sebelum dan sesudah menembak.</span>
          </li>
        </ul>
      ),
    },
    {
      title: 'Pelanggaran & Sanksi: DQ, DNS, DNF, Penalty',
      icon: XCircle,
      content: (
        <div className="space-y-3 text-xs sm:text-sm text-neutral-600 dark:text-neutral-300">
          <div>
            <strong className="text-red-600 dark:text-red-400 block mb-1">Diskualifikasi (DQ):</strong>
            <p>Diberikan jika terjadi tembakan tidak terarah (&lt; 3m dari petembak), menembak sebelum ada instruksi RO, tembak silang sasaran petembak lain, melanggar batas sudut 60°, atau terjadi letusan yang tidak pada tempatnya.</p>
          </div>
          <div>
            <strong className="text-amber-600 dark:text-amber-400 block mb-1">Penalty:</strong>
            <p>Apabila waktu telah habis namun petembak masih melakukan penembakan, maka dikenakan pemotongan nilai perkenaan terbesar.</p>
          </div>
          <div>
            <strong className="text-neutral-700 dark:text-neutral-300 block mb-1">DNS &amp; DNF:</strong>
            <p><strong>DNS (Did Not Shoot):</strong> Nilai dinyatakan 0 dan tidak diberikan kesempatan ulang. <strong>DNF (Did Not Finish):</strong> Nilai dihitung hanya berdasarkan tembakan yang telah terlaksana.</p>
          </div>
          <div className="p-3 rounded-lg bg-neutral-100 dark:bg-[#161e2b] border border-neutral-200 dark:border-neutral-800 text-xs">
            <em>Catatan: Hasil penilaian lembar score sheet yang telah ditandatangani bersama wasit/juri bersifat mutlak dan tidak dapat diganggu gugat.</em>
          </div>
        </div>
      ),
    },
  ];

  return (
    <section id="aturan" className="py-20 lg:py-28">
      <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="text-center mb-16">
          <div className="inline-flex items-center gap-2 text-xs font-semibold text-brand-600 dark:text-brand-400 uppercase tracking-widest mb-3">
            <ShieldAlert className="w-3.5 h-3.5" />
            <span>Petunjuk Teknis Pertandingan</span>
          </div>
          <h2 className="font-heading font-bold text-3xl sm:text-4xl text-neutral-900 dark:text-white uppercase tracking-tight">
            Aturan &amp; Ketentuan Kejuaraan
          </h2>
          <p className="text-neutral-600 dark:text-neutral-400 text-sm sm:text-base mt-2">
            Disusun berdasarkan regulasi resmi Juknis BDA Shooting Championship 2026.
          </p>
        </div>

        {/* Accordion Component */}
        <div className="space-y-4">
          {rulesList.map((item, idx) => {
            const isOpen = openIndex === idx;
            const Icon = item.icon;
            return (
              <div
                key={idx}
                className="bg-white dark:bg-[#1a2332] rounded-2xl border border-neutral-200 dark:border-neutral-800 shadow-sm overflow-hidden transition-colors"
              >
                <button
                  onClick={() => toggleAccordion(idx)}
                  className="w-full px-6 py-5 flex items-center justify-between text-left hover:bg-neutral-50 dark:hover:bg-neutral-800/40 transition-colors"
                >
                  <div className="flex items-center gap-3">
                    <div className="w-8 h-8 rounded-lg bg-brand-500/10 text-brand-500 flex items-center justify-center shrink-0">
                      <Icon className="w-4 h-4 stroke-[2.2]" />
                    </div>
                    <span className="font-semibold text-base sm:text-lg text-neutral-900 dark:text-white">
                      {item.title}
                    </span>
                  </div>
                  <ChevronDown
                    className={`w-5 h-5 text-neutral-400 transition-transform duration-300 ${
                      isOpen ? 'rotate-180 text-brand-500' : ''
                    }`}
                  />
                </button>

                {isOpen && (
                  <div className="px-6 pb-6 pt-2 border-t border-neutral-100 dark:border-neutral-800/80">
                    {item.content}
                  </div>
                )}
              </div>
            );
          })}
        </div>
      </div>
    </section>
  );
}
