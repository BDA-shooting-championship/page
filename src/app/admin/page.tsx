'use client';

import React, { useState, useEffect } from 'react';
import Link from 'next/link';
import { 
  ShieldCheck, Lock, LogOut, Search, Filter, CheckCircle2, XCircle, Clock, 
  Eye, MessageCircle, Copy, Check, Download, RefreshCw, ExternalLink, AlertTriangle, ArrowLeft
} from 'lucide-react';
import { CONFIG } from '@/lib/config';
import { fetchRegistrations, updateRegistrationStatus } from '@/lib/api';
import { RegistrationRecord, buildWhatsAppTicketUrl } from '@/lib/whatsapp';
import { formatDateIndo } from '@/lib/utils';

export default function AdminPage() {
  const [isAuthenticated, setIsAuthenticated] = useState(false);
  const [passwordInput, setPasswordInput] = useState('');
  const [loginError, setLoginError] = useState(false);

  const [registrations, setRegistrations] = useState<RegistrationRecord[]>([]);
  const [isLoading, setIsLoading] = useState(false);
  const [searchQuery, setSearchQuery] = useState('');
  const [statusFilter, setStatusFilter] = useState<string>('all');
  const [selectedReg, setSelectedReg] = useState<RegistrationRecord | null>(null);

  const [adminNotes, setAdminNotes] = useState('');
  const [isUpdating, setIsUpdating] = useState(false);
  const [copiedLink, setCopiedLink] = useState(false);

  // Cek session login admin
  useEffect(() => {
    const sessionAuth = sessionStorage.getItem('bda_admin_auth');
    if (sessionAuth === 'true') {
      setIsAuthenticated(true);
      loadRegistrations();
    }
  }, []);

  const handleLogin = (e: React.FormEvent) => {
    e.preventDefault();
    if (passwordInput === CONFIG.ADMIN_PASSWORD) {
      sessionStorage.setItem('bda_admin_auth', 'true');
      setIsAuthenticated(true);
      setLoginError(false);
      loadRegistrations();
    } else {
      setLoginError(true);
    }
  };

  const handleLogout = () => {
    sessionStorage.removeItem('bda_admin_auth');
    setIsAuthenticated(false);
    setPasswordInput('');
  };

  const loadRegistrations = async () => {
    setIsLoading(true);
    try {
      const data = await fetchRegistrations(CONFIG.ADMIN_TOKEN);
      setRegistrations(data);
    } catch (err) {
      console.error('Failed to load registrations', err);
    } finally {
      setIsLoading(false);
    }
  };

  const handleUpdateStatus = async (newStatus: 'Pending' | 'Verified' | 'Rejected') => {
    if (!selectedReg) return;
    setIsUpdating(true);
    try {
      const res = await updateRegistrationStatus(
        selectedReg.registration_id,
        newStatus,
        adminNotes,
        CONFIG.ADMIN_TOKEN
      );

      // Perbarui state lokal
      const updated = registrations.map((r) => {
        if (r.registration_id === selectedReg.registration_id) {
          return {
            ...r,
            status: newStatus,
            no_peserta: res.noPeserta || r.no_peserta,
            admin_notes: adminNotes,
          };
        }
        return r;
      });

      setRegistrations(updated);
      setSelectedReg({
        ...selectedReg,
        status: newStatus,
        no_peserta: res.noPeserta || selectedReg.no_peserta,
        admin_notes: adminNotes,
      });
    } catch (err: any) {
      alert(err.message || 'Gagal memperbarui status');
    } finally {
      setIsUpdating(false);
    }
  };

  // Export to CSV
  const handleExportCSV = () => {
    if (registrations.length === 0) return;

    const headers = ['No', 'ID Registrasi', 'No Peserta', 'Nama', 'Pangkat', 'NRP', 'Satuan', 'Kategori', 'Telepon', 'Email', 'Status', 'Catatan', 'Tanggal Daftar'];
    const rows = registrations.map((r, i) => [
      i + 1,
      r.registration_id,
      r.no_peserta || '-',
      `"${r.nama.replace(/"/g, '""')}"`,
      r.pangkat,
      `'${r.nrp}`,
      `"${r.satuan.replace(/"/g, '""')}"`,
      `"${r.kategori.replace(/"/g, '""')}"`,
      `'${r.telepon}`,
      r.email,
      r.status,
      `"${(r.admin_notes || '').replace(/"/g, '""')}"`,
      r.created_at ? formatDateIndo(r.created_at) : '-'
    ]);

    const csvContent = 'data:text/csv;charset=utf-8,\uFEFF' + [headers.join(','), ...rows.map(e => e.join(','))].join('\n');
    const encodedUri = encodeURI(csvContent);
    const link = document.createElement('a');
    link.setAttribute('href', encodedUri);
    link.setAttribute('download', `peserta_bda_2026_${new Date().toISOString().slice(0, 10)}.csv`);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
  };

  // Filter Data
  const filteredRegistrations = registrations.filter((r) => {
    const matchesSearch = 
      r.nama.toLowerCase().includes(searchQuery.toLowerCase()) ||
      r.nrp.toLowerCase().includes(searchQuery.toLowerCase()) ||
      r.satuan.toLowerCase().includes(searchQuery.toLowerCase()) ||
      (r.no_peserta && r.no_peserta.toLowerCase().includes(searchQuery.toLowerCase()));

    const matchesStatus = statusFilter === 'all' || r.status === statusFilter;
    return matchesSearch && matchesStatus;
  });

  // Statistik Singkat
  const totalCount = registrations.length;
  const verifiedCount = registrations.filter(r => r.status === 'Verified').length;
  const pendingCount = registrations.filter(r => r.status === 'Pending').length;
  const rejectedCount = registrations.filter(r => r.status === 'Rejected').length;

  // Jika Belum Login
  if (!isAuthenticated) {
    return (
      <div className="pt-36 pb-24 min-h-screen bg-neutral-50 dark:bg-[#0f1419] flex items-center justify-center px-4">
        <div className="max-w-md w-full bg-white dark:bg-[#1a2332] rounded-3xl border border-neutral-200 dark:border-neutral-800 p-8 shadow-xl text-center">
          <div className="w-14 h-14 rounded-2xl bg-brand-500/10 text-brand-500 flex items-center justify-center mx-auto mb-5">
            <Lock className="w-7 h-7 stroke-[2.2]" />
          </div>

          <h2 className="font-heading font-bold text-2xl text-neutral-900 dark:text-white uppercase tracking-tight">
            Portal Admin Panitia
          </h2>
          <p className="text-xs text-neutral-500 dark:text-neutral-400 mt-1 mb-6">
            BDA Shooting Championship 2026
          </p>

          <form onSubmit={handleLogin} className="space-y-4 text-left">
            <div>
              <label className="block text-xs font-semibold uppercase tracking-wider text-neutral-600 dark:text-neutral-400 mb-1.5">
                Password Admin
              </label>
              <input
                type="password"
                required
                value={passwordInput}
                onChange={(e) => setPasswordInput(e.target.value)}
                placeholder="Masukkan password..."
                className="w-full px-4 py-3 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-neutral-50 dark:bg-[#151c27] text-neutral-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-brand-500"
              />
            </div>

            {loginError && (
              <p className="text-xs text-red-500 font-medium">
                Password salah. Silakan coba lagi.
              </p>
            )}

            <button
              type="submit"
              className="w-full py-3 rounded-xl font-semibold text-sm bg-brand-500 hover:bg-brand-600 text-white shadow-md transition-colors"
            >
              Masuk Dashboard
            </button>
          </form>

          <div className="mt-6 pt-4 border-t border-neutral-100 dark:border-neutral-800 text-xs text-neutral-400">
            <Link href="/" className="hover:text-brand-500 transition-colors">
              ← Kembali ke Beranda
            </Link>
          </div>
        </div>
      </div>
    );
  }

  // Tampilan Dashboard Admin
  return (
    <div className="pt-28 pb-20 min-h-screen bg-neutral-50 dark:bg-[#0f1419]">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {/* Top Bar Dashboard */}
        <div className="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-8">
          <div>
            <div className="inline-flex items-center gap-2 text-xs font-semibold text-brand-600 dark:text-brand-400 uppercase tracking-widest mb-1">
              <ShieldCheck className="w-4 h-4" />
              <span>Admin Management Portal</span>
            </div>
            <h1 className="font-heading font-bold text-2xl sm:text-3xl text-neutral-900 dark:text-white uppercase tracking-tight">
              Verifikasi &amp; E-Ticket Peserta
            </h1>
          </div>

          <div className="flex items-center gap-3">
            <button
              onClick={loadRegistrations}
              disabled={isLoading}
              className="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-semibold bg-white dark:bg-[#1a2332] border border-neutral-200 dark:border-neutral-800 text-neutral-700 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800 transition-colors shadow-sm"
            >
              <RefreshCw className={`w-3.5 h-3.5 ${isLoading ? 'animate-spin' : ''}`} />
              <span>Refresh</span>
            </button>

            <button
              onClick={handleExportCSV}
              className="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-semibold bg-emerald-600 hover:bg-emerald-700 text-white transition-colors shadow-sm"
            >
              <Download className="w-3.5 h-3.5" />
              <span>Export CSV</span>
            </button>

            <button
              onClick={handleLogout}
              className="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-semibold border border-red-200 dark:border-red-900/50 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/30 transition-colors"
            >
              <LogOut className="w-3.5 h-3.5" />
              <span>Logout</span>
            </button>
          </div>
        </div>

        {/* Stat Cards */}
        <div className="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
          <div className="p-5 rounded-2xl bg-white dark:bg-[#1a2332] border border-neutral-200 dark:border-neutral-800 shadow-sm">
            <span className="text-xs uppercase font-semibold text-neutral-500 dark:text-neutral-400 block mb-1">
              Total Pendaftar
            </span>
            <span className="font-heading font-bold text-3xl text-neutral-900 dark:text-white">
              {totalCount}
            </span>
          </div>

          <div className="p-5 rounded-2xl bg-white dark:bg-[#1a2332] border border-neutral-200 dark:border-neutral-800 shadow-sm">
            <span className="text-xs uppercase font-semibold text-emerald-600 dark:text-emerald-400 block mb-1">
              Terverifikasi (Lunas)
            </span>
            <span className="font-heading font-bold text-3xl text-emerald-600 dark:text-emerald-400">
              {verifiedCount}
            </span>
          </div>

          <div className="p-5 rounded-2xl bg-white dark:bg-[#1a2332] border border-neutral-200 dark:border-neutral-800 shadow-sm">
            <span className="text-xs uppercase font-semibold text-amber-600 dark:text-amber-400 block mb-1">
              Menunggu Verifikasi
            </span>
            <span className="font-heading font-bold text-3xl text-amber-600 dark:text-amber-400">
              {pendingCount}
            </span>
          </div>

          <div className="p-5 rounded-2xl bg-white dark:bg-[#1a2332] border border-neutral-200 dark:border-neutral-800 shadow-sm">
            <span className="text-xs uppercase font-semibold text-red-600 dark:text-red-400 block mb-1">
              Ditolak / Batal
            </span>
            <span className="font-heading font-bold text-3xl text-red-600 dark:text-red-400">
              {rejectedCount}
            </span>
          </div>
        </div>

        {/* Filter & Pencarian Bar */}
        <div className="p-4 rounded-2xl bg-white dark:bg-[#1a2332] border border-neutral-200 dark:border-neutral-800 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4 mb-6">
          <div className="relative w-full sm:w-80">
            <Search className="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-neutral-400" />
            <input
              type="text"
              value={searchQuery}
              onChange={(e) => setSearchQuery(e.target.value)}
              placeholder="Cari Nama, NRP, atau Satuan..."
              className="w-full pl-10 pr-4 py-2 text-xs rounded-xl border border-neutral-200 dark:border-neutral-700 bg-neutral-50 dark:bg-[#151c27] text-neutral-900 dark:text-white placeholder-neutral-400 focus:outline-none focus:ring-2 focus:ring-brand-500"
            />
          </div>

          <div className="flex items-center gap-2 w-full sm:w-auto">
            <Filter className="w-4 h-4 text-neutral-400 shrink-0" />
            <select
              value={statusFilter}
              onChange={(e) => setStatusFilter(e.target.value)}
              className="text-xs px-3 py-2 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-neutral-50 dark:bg-[#151c27] text-neutral-900 dark:text-white focus:outline-none"
            >
              <option value="all">Semua Status</option>
              <option value="Pending">Menunggu Verifikasi (Pending)</option>
              <option value="Verified">Terverifikasi (Verified)</option>
              <option value="Rejected">Ditolak (Rejected)</option>
            </select>
          </div>
        </div>

        {/* Tabel Data Peserta */}
        <div className="bg-white dark:bg-[#1a2332] rounded-2xl border border-neutral-200 dark:border-neutral-800 shadow-sm overflow-hidden">
          <div className="overflow-x-auto">
            <table className="w-full text-left text-xs">
              <thead className="bg-neutral-100/70 dark:bg-[#151c27] text-neutral-600 dark:text-neutral-400 border-b border-neutral-200 dark:border-neutral-800 font-semibold uppercase tracking-wider">
                <tr>
                  <th className="py-3.5 px-4">No.</th>
                  <th className="py-3.5 px-4">No. Peserta</th>
                  <th className="py-3.5 px-4">Nama Lengkap</th>
                  <th className="py-3.5 px-4">Pangkat / NRP</th>
                  <th className="py-3.5 px-4">Satuan / Club</th>
                  <th className="py-3.5 px-4">Kategori Lomba</th>
                  <th className="py-3.5 px-4">Status</th>
                  <th className="py-3.5 px-4 text-right">Aksi</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-neutral-200 dark:divide-neutral-800/80 text-neutral-800 dark:text-neutral-200">
                {filteredRegistrations.length === 0 ? (
                  <tr>
                    <td colSpan={8} className="py-12 text-center text-neutral-400">
                      Belum ada data pendaftar yang cocok.
                    </td>
                  </tr>
                ) : (
                  filteredRegistrations.map((reg, idx) => (
                    <tr
                      key={reg.registration_id}
                      className="hover:bg-neutral-50 dark:hover:bg-[#161f2e] transition-colors"
                    >
                      <td className="py-3.5 px-4 font-mono text-neutral-500">{idx + 1}</td>
                      <td className="py-3.5 px-4">
                        {reg.no_peserta ? (
                          <span className="font-mono font-bold text-brand-500 bg-brand-500/10 px-2.5 py-0.5 rounded-md">
                            {reg.no_peserta}
                          </span>
                        ) : (
                          <span className="text-neutral-400 italic">Belum diverifikasi</span>
                        )}
                      </td>
                      <td className="py-3.5 px-4 font-semibold">{reg.nama}</td>
                      <td className="py-3.5 px-4">{reg.pangkat} ({reg.nrp})</td>
                      <td className="py-3.5 px-4">{reg.satuan}</td>
                      <td className="py-3.5 px-4">{reg.kategori}</td>
                      <td className="py-3.5 px-4">
                        {reg.status === 'Verified' && (
                          <span className="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">
                            <CheckCircle2 className="w-3 h-3" />
                            <span>Lunas (Verified)</span>
                          </span>
                        )}
                        {reg.status === 'Pending' && (
                          <span className="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20">
                            <Clock className="w-3 h-3" />
                            <span>Pending</span>
                          </span>
                        )}
                        {reg.status === 'Rejected' && (
                          <span className="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-red-500/10 text-red-600 dark:text-red-400 border border-red-500/20">
                            <XCircle className="w-3 h-3" />
                            <span>Ditolak</span>
                          </span>
                        )}
                      </td>
                      <td className="py-3.5 px-4 text-right">
                        <button
                          onClick={() => {
                            setSelectedReg(reg);
                            setAdminNotes(reg.admin_notes || '');
                          }}
                          className="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-brand-500 text-white hover:bg-brand-600 transition-colors shadow-sm"
                        >
                          <Eye className="w-3.5 h-3.5" />
                          <span>Periksa &amp; Aksi</span>
                        </button>
                      </td>
                    </tr>
                  ))
                )}
              </tbody>
            </table>
          </div>
        </div>
      </div>

      {/* Modal Detail & Verifikasi Bukti Transfer */}
      {selectedReg && (
        <div className="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4 overflow-y-auto">
          <div className="bg-white dark:bg-[#1a2332] rounded-3xl border border-neutral-200 dark:border-neutral-800 max-w-3xl w-full my-8 p-6 sm:p-8 shadow-2xl space-y-6">
            <div className="flex items-start justify-between pb-4 border-b border-neutral-100 dark:border-neutral-800">
              <div>
                <span className="text-xs font-semibold uppercase tracking-wider text-brand-600 dark:text-brand-400">
                  Verifikasi Berkas &amp; Pembayaran
                </span>
                <h3 className="font-heading font-bold text-2xl text-neutral-900 dark:text-white uppercase mt-1">
                  {selectedReg.nama}
                </h3>
                <p className="text-xs text-neutral-500">
                  ID Registrasi: <span className="font-mono text-brand-500">{selectedReg.registration_id}</span>
                </p>
              </div>

              <button
                onClick={() => setSelectedReg(null)}
                className="p-2 rounded-xl text-neutral-400 hover:text-neutral-700 dark:hover:text-white hover:bg-neutral-100 dark:hover:bg-neutral-800"
              >
                ✕
              </button>
            </div>

            {/* Info Rincian */}
            <div className="grid grid-cols-2 sm:grid-cols-4 gap-4 p-4 rounded-2xl bg-neutral-50 dark:bg-[#151c27] text-xs">
              <div>
                <span className="text-neutral-500 block">Pangkat / NRP:</span>
                <span className="font-semibold text-neutral-900 dark:text-white">{selectedReg.pangkat} / {selectedReg.nrp}</span>
              </div>
              <div>
                <span className="text-neutral-500 block">Satuan:</span>
                <span className="font-semibold text-neutral-900 dark:text-white">{selectedReg.satuan}</span>
              </div>
              <div>
                <span className="text-neutral-500 block">WhatsApp:</span>
                <span className="font-semibold text-neutral-900 dark:text-white">{selectedReg.telepon}</span>
              </div>
              <div>
                <span className="text-neutral-500 block">No. Peserta:</span>
                <span className="font-mono font-bold text-brand-500">{selectedReg.no_peserta || '(Belum dibuat)'}</span>
              </div>
            </div>

            {/* Bukti Transfer & Foto KTA Preview */}
            <div className="grid grid-cols-1 sm:grid-cols-2 gap-6">
              {/* Bukti Transfer */}
              <div className="space-y-2">
                <label className="text-xs font-semibold uppercase tracking-wider text-neutral-700 dark:text-neutral-300 block">
                  Bukti Transfer Pembayaran:
                </label>
                <div className="h-64 rounded-2xl border border-neutral-200 dark:border-neutral-700 bg-neutral-100 dark:bg-[#151c27] overflow-hidden flex items-center justify-center p-2 relative group">
                  {selectedReg.bukti_url ? (
                    <>
                      {/* eslint-disable-next-line @next/next/no-img-element */}
                      <img src={selectedReg.bukti_url} alt="Bukti Transfer" className="max-h-full max-w-full object-contain rounded-lg" />
                      <a
                        href={selectedReg.bukti_url}
                        target="_blank"
                        rel="noreferrer"
                        className="absolute bottom-3 right-3 px-3 py-1.5 rounded-lg bg-black/70 text-white text-xs font-medium flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity"
                      >
                        <ExternalLink className="w-3.5 h-3.5" />
                        <span>Buka Layar Penuh</span>
                      </a>
                    </>
                  ) : (
                    <span className="text-xs text-neutral-400 italic">Tidak ada gambar bukti transfer</span>
                  )}
                </div>
              </div>

              {/* Foto KTA */}
              <div className="space-y-2">
                <label className="text-xs font-semibold uppercase tracking-wider text-neutral-700 dark:text-neutral-300 block">
                  Foto KTA / Tanda Pengenal:
                </label>
                <div className="h-64 rounded-2xl border border-neutral-200 dark:border-neutral-700 bg-neutral-100 dark:bg-[#151c27] overflow-hidden flex items-center justify-center p-2 relative group">
                  {selectedReg.kta_url ? (
                    <>
                      {/* eslint-disable-next-line @next/next/no-img-element */}
                      <img src={selectedReg.kta_url} alt="Foto KTA" className="max-h-full max-w-full object-contain rounded-lg" />
                      <a
                        href={selectedReg.kta_url}
                        target="_blank"
                        rel="noreferrer"
                        className="absolute bottom-3 right-3 px-3 py-1.5 rounded-lg bg-black/70 text-white text-xs font-medium flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity"
                      >
                        <ExternalLink className="w-3.5 h-3.5" />
                        <span>Buka Layar Penuh</span>
                      </a>
                    </>
                  ) : (
                    <span className="text-xs text-neutral-400 italic">Tidak ada gambar KTA</span>
                  )}
                </div>
              </div>
            </div>

            {/* Catatan Admin */}
            <div>
              <label className="block text-xs font-semibold uppercase tracking-wider text-neutral-600 dark:text-neutral-400 mb-1.5">
                Catatan Verifikasi Panitia (Opsional):
              </label>
              <input
                type="text"
                value={adminNotes}
                onChange={(e) => setAdminNotes(e.target.value)}
                placeholder="Contoh: Sudah dicek mutasi rekening BRI tgl 24..."
                className="w-full px-4 py-2.5 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-neutral-50 dark:bg-[#151c27] text-neutral-900 dark:text-white text-xs"
              />
            </div>

            {/* Aksi Verifikasi / Tolak */}
            <div className="pt-2 flex flex-col sm:flex-row items-center justify-between gap-4 border-t border-neutral-100 dark:border-neutral-800">
              <div className="flex items-center gap-2 w-full sm:w-auto">
                <button
                  onClick={() => handleUpdateStatus('Verified')}
                  disabled={isUpdating}
                  className="flex-1 sm:flex-none inline-flex items-center justify-center gap-1.5 px-5 py-2.5 rounded-xl text-xs font-semibold bg-emerald-600 hover:bg-emerald-700 text-white shadow-md transition-colors"
                >
                  <CheckCircle2 className="w-4 h-4" />
                  <span>Konfirmasi Pembayaran (Lunas)</span>
                </button>

                <button
                  onClick={() => handleUpdateStatus('Rejected')}
                  disabled={isUpdating}
                  className="flex-1 sm:flex-none inline-flex items-center justify-center gap-1.5 px-4 py-2.5 rounded-xl text-xs font-semibold bg-red-600 hover:bg-red-700 text-white transition-colors"
                >
                  <XCircle className="w-4 h-4" />
                  <span>Tolak</span>
                </button>
              </div>

              {/* Tombol Kirim WhatsApp & E-Ticket Link (Aktif Jika Status Verified) */}
              {selectedReg.status === 'Verified' && (
                <div className="flex items-center gap-2 w-full sm:w-auto">
                  <a
                    href={buildWhatsAppTicketUrl(selectedReg)}
                    target="_blank"
                    rel="noopener noreferrer"
                    className="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl text-xs font-semibold bg-emerald-500 hover:bg-emerald-600 text-white shadow-lg shadow-emerald-500/25 transition-all"
                  >
                    <MessageCircle className="w-4 h-4" />
                    <span>Kirim E-Ticket via WhatsApp</span>
                  </a>

                  <Link
                    href={`/e-ticket/?id=${encodeURIComponent(selectedReg.registration_id)}`}
                    target="_blank"
                    className="p-2.5 rounded-xl border border-neutral-200 dark:border-neutral-700 hover:bg-neutral-100 dark:hover:bg-neutral-800 text-neutral-700 dark:text-neutral-300 transition-colors"
                    title="Buka Tampilan E-Ticket"
                  >
                    <ExternalLink className="w-4 h-4" />
                  </Link>
                </div>
              )}
            </div>
          </div>
        </div>
      )}
    </div>
  );
}
