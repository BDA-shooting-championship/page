<?php
$pageTitle = 'Pendaftaran Peserta — BDA Shooting Championship 2026';
$currentPage = 'daftar';
require_once __DIR__ . '/includes/header.php';
?>

<div class="py-12 bg-gray-50 dark:bg-gray-900 min-h-screen" x-data="registrationForm()">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Success State -->
        <template x-if="successData">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8 border border-green-200 dark:border-green-800 text-center animate-fade-in-up">
                <div class="w-16 h-16 bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="check-circle-2" class="w-10 h-10"></i>
                </div>
                <h2 class="font-display text-3xl font-bold mb-2 text-gray-900 dark:text-white">Pendaftaran Berhasil!</h2>
                <p class="text-gray-600 dark:text-gray-300 text-sm mb-6">
                    Data pendaftaran Anda telah kami terima dan sedang dalam antrean verifikasi pembayaran oleh panitia.
                </p>

                <div class="bg-gray-50 dark:bg-gray-900/50 rounded-xl p-5 border border-gray-200 dark:border-gray-700 text-left max-w-lg mx-auto mb-6 space-y-2 text-sm">
                    <div class="flex justify-between items-center pb-2 border-b border-gray-200 dark:border-gray-700">
                        <span class="text-gray-500">ID Pendaftaran</span>
                        <span class="font-mono font-bold text-copper-600 dark:text-copper-400 text-base" x-text="successData.registrationId"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Nama Lengkap</span>
                        <span class="font-semibold text-gray-800 dark:text-gray-200" x-text="formData.nama"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Pangkat / NRP</span>
                        <span class="text-gray-800 dark:text-gray-200" x-text="`${formData.pangkat} / ${formData.nrp}`"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Satuan / Club</span>
                        <span class="text-gray-800 dark:text-gray-200" x-text="formData.satuan"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Kategori</span>
                        <span class="font-medium text-gray-800 dark:text-gray-200" x-text="selectedKategori.join(', ')"></span>
                    </div>
                    <div class="flex justify-between pt-2 border-t border-gray-200 dark:border-gray-700 font-bold">
                        <span class="text-gray-700 dark:text-gray-300">Total Biaya</span>
                        <span class="text-copper-600 dark:text-copper-400" x-text="formatRupiah(totalBiaya)"></span>
                    </div>
                </div>

                <div class="p-4 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-xl text-xs text-amber-800 dark:text-amber-300 text-left max-w-lg mx-auto mb-6">
                    <p class="font-semibold mb-1 flex items-center gap-1.5"><i data-lucide="info" class="w-4 h-4"></i> Informasi Selanjutnya:</p>
                    <p>Setelah pembayaran Anda dikonfirmasi oleh panitia, E-Ticket resmi beserta QR Code unik dan Nomor Peserta (BSC-xxx) akan otomatis dikirimkan ke WhatsApp Anda di <strong x-text="formData.telepon"></strong>.</p>
                </div>

                <div class="flex flex-col sm:flex-row justify-center gap-4">
                    <a :href="`/e-ticket.php?id=${encodeURIComponent(successData.registrationId)}`" class="px-6 py-3 bg-copper-600 hover:bg-copper-700 text-white font-semibold rounded-lg transition shadow-md flex items-center justify-center gap-2">
                        <i data-lucide="ticket" class="w-4 h-4"></i> Cek Status E-Ticket
                    </a>
                    <a href="/" class="px-6 py-3 border border-gray-300 dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-800 font-semibold rounded-lg transition flex items-center justify-center gap-2">
                        <i data-lucide="home" class="w-4 h-4"></i> Kembali ke Beranda
                    </a>
                </div>
            </div>
        </template>

        <!-- Form State -->
        <template x-if="!successData">
            <div>
                <!-- Header Banner -->
                <div class="text-center mb-8">
                    <div class="flex justify-center items-center gap-4 mb-4">
                        <img src="/assets/logo-bda.png" alt="Logo BDA 750" class="h-16 w-16 object-contain drop-shadow-md">
                        <img src="/assets/logo-championship.png" alt="Logo BSC 2026" class="h-16 w-16 object-contain drop-shadow-md">
                    </div>
                    <span class="px-3.5 py-1 text-xs font-semibold bg-copper-100 dark:bg-copper-900/30 text-copper-700 dark:text-copper-400 rounded-full uppercase tracking-wider">
                        Formulir Registrasi
                    </span>
                    <h1 class="font-display text-3xl sm:text-4xl font-bold mt-3 text-gray-900 dark:text-white">
                        Pendaftaran Peserta BSC 2026
                    </h1>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-2 max-w-xl mx-auto">
                        Isi biodata Anda dengan benar. Pastikan KTA dan bukti transfer pembayaran terbaca dengan jelas.
                    </p>
                </div>

                <!-- Info Bank Box -->
                <div class="mb-8 p-5 bg-gradient-to-r from-copper-50 to-orange-50 dark:from-gray-800 dark:to-gray-800/80 border border-copper-200 dark:border-copper-800/50 rounded-2xl shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4" x-data="{ copied: false }">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs uppercase font-bold tracking-wider text-copper-600 dark:text-copper-400">Pembayaran Pendaftaran</span>
                            <span class="text-xs px-2 py-0.5 rounded bg-copper-200/60 dark:bg-copper-900/50 text-copper-800 dark:text-copper-300 font-medium">Rp 200.000 / Kategori</span>
                        </div>
                        <p class="text-sm font-semibold text-gray-800 dark:text-gray-200 mt-1">
                            Bank BRI — <span class="font-mono text-base font-bold text-copper-700 dark:text-copper-300">053801071906503</span> (a.n. Ahyandi Hi Karim)
                        </p>
                    </div>
                    <button type="button" @click="navigator.clipboard.writeText('053801071906503'); copied = true; setTimeout(() => copied = false, 2000)" 
                            class="px-4 py-2 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 text-xs font-semibold rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition shrink-0 flex items-center gap-1.5 shadow-sm">
                        <i data-lucide="copy" class="w-3.5 h-3.5" x-show="!copied"></i>
                        <i data-lucide="check" class="w-3.5 h-3.5 text-green-600" x-show="copied" x-cloak></i>
                        <span x-text="copied ? 'Tersalin!' : 'Salin Rekening'"></span>
                    </button>
                </div>

                <!-- Alert Error -->
                <div x-show="errorMessage" x-cloak class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl text-sm text-red-700 dark:text-red-400 flex items-start gap-3">
                    <i data-lucide="alert-circle" class="w-5 h-5 shrink-0 mt-0.5"></i>
                    <span x-text="errorMessage"></span>
                </div>

                <!-- Form Card -->
                <form @submit.prevent="submitForm" class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6 sm:p-10 border border-gray-200 dark:border-gray-700 space-y-6">
                    
                    <!-- 1. Nama Lengkap -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-2">
                            1. Nama Lengkap *
                        </label>
                        <input type="text" x-model="formData.nama" required placeholder="Contoh: Huggies Yustisio" 
                               class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-copper-500 focus:outline-none transition">
                    </div>

                    <!-- 2 & 3. Email & No. Telp -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-2">
                                2. Email Aktif *
                            </label>
                            <input type="email" x-model="formData.email" required placeholder="nama@email.com" 
                                   class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-copper-500 focus:outline-none transition">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-2">
                                3. No. WhatsApp *
                            </label>
                            <input type="tel" x-model="formData.telepon" required placeholder="Contoh: 085272377704" 
                                   class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-copper-500 focus:outline-none transition">
                            <span class="text-[11px] text-gray-500 mt-1 block">E-Ticket resmi akan dikirim ke nomor ini</span>
                        </div>
                    </div>

                    <!-- 4 & 5. Pangkat & NRP -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-2">
                                4. Pangkat *
                            </label>
                            <input type="text" x-model="formData.pangkat" required placeholder="Contoh: Briptu / Ipda / Sipil" 
                                   class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-copper-500 focus:outline-none transition">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-2">
                                5. NRP / NIP / NIK *
                            </label>
                            <input type="text" x-model="formData.nrp" required placeholder="Contoh: 00120207" 
                                   class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-copper-500 focus:outline-none transition">
                        </div>
                    </div>

                    <!-- 6. Satuan / Club -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-2">
                            6. Satuan / Club Menembak *
                        </label>
                        <input type="text" x-model="formData.satuan" required placeholder="Contoh: Batalyon A Resimen I Pasukan Pelopor / Perbakin" 
                               class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-copper-500 focus:outline-none transition">
                    </div>

                    <!-- 7. Kategori Pertandingan -->
                    <div class="pt-2">
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-2">
                            7. Kategori Pertandingan (Pilih salah satu atau keduanya) *
                        </label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Presisi 20M -->
                            <div @click="toggleKategori('Pistol Presisi 20M')" 
                                 :class="selectedKategori.includes('Pistol Presisi 20M') ? 'border-copper-500 bg-copper-50/50 dark:bg-copper-900/20 ring-2 ring-copper-500/20' : 'border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50'"
                                 class="p-4 rounded-xl border cursor-pointer transition flex items-start justify-between">
                                <div>
                                    <h4 class="font-display font-semibold text-base text-gray-900 dark:text-white">Pistol Presisi 20M</h4>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">10 seri tembakan lesan ring, jarak 20 meter</p>
                                    <span class="inline-block mt-2 text-xs font-bold text-copper-600 dark:text-copper-400">Rp 200.000</span>
                                </div>
                                <div class="w-5 h-5 rounded border flex items-center justify-center shrink-0 transition"
                                     :class="selectedKategori.includes('Pistol Presisi 20M') ? 'bg-copper-600 border-copper-600 text-white' : 'border-gray-300 dark:border-gray-600'">
                                    <i data-lucide="check" class="w-3.5 h-3.5" x-show="selectedKategori.includes('Pistol Presisi 20M')"></i>
                                </div>
                            </div>

                            <!-- Dueling Plat -->
                            <div @click="toggleKategori('Dueling Plat')" 
                                 :class="selectedKategori.includes('Dueling Plat') ? 'border-copper-500 bg-copper-50/50 dark:bg-copper-900/20 ring-2 ring-copper-500/20' : 'border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50'"
                                 class="p-4 rounded-xl border cursor-pointer transition flex items-start justify-between">
                                <div>
                                    <h4 class="font-display font-semibold text-base text-gray-900 dark:text-white">Dueling Plat</h4>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Sistem gugur head-to-head, 5 plat + 1 popper</p>
                                    <span class="inline-block mt-2 text-xs font-bold text-copper-600 dark:text-copper-400">Rp 200.000</span>
                                </div>
                                <div class="w-5 h-5 rounded border flex items-center justify-center shrink-0 transition"
                                     :class="selectedKategori.includes('Dueling Plat') ? 'bg-copper-600 border-copper-600 text-white' : 'border-gray-300 dark:border-gray-600'">
                                    <i data-lucide="check" class="w-3.5 h-3.5" x-show="selectedKategori.includes('Dueling Plat')"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Total Biaya Summary -->
                        <div class="mt-4 p-4 rounded-xl bg-gray-100 dark:bg-gray-900 flex items-center justify-between">
                            <span class="text-xs font-semibold text-gray-600 dark:text-gray-300">
                                Total Biaya: (<span x-text="selectedKategori.length"></span> Kategori)
                            </span>
                            <span class="font-display font-bold text-xl text-copper-600 dark:text-copper-400" x-text="formatRupiah(totalBiaya)"></span>
                        </div>
                    </div>

                    <!-- 8. Upload Foto KTA -->
                    <div class="pt-2">
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-2">
                            8. Upload Foto KTA / KTP / Tanda Pengenal * (Maks. 2MB)
                        </label>
                        <div class="flex flex-col sm:flex-row items-center gap-4">
                            <label class="w-full sm:flex-1 flex flex-col items-center justify-center p-6 border-2 border-dashed border-gray-300 dark:border-gray-700 rounded-xl hover:border-copper-500 dark:hover:border-copper-500 transition cursor-pointer bg-gray-50/50 dark:bg-gray-900/50">
                                <i data-lucide="upload" class="w-6 h-6 text-gray-400 mb-2"></i>
                                <span class="text-xs font-semibold text-gray-700 dark:text-gray-300" x-text="ktaFileName || 'Pilih foto KTA (JPG, PNG, WebP)'"></span>
                                <span class="text-[10px] text-gray-400 mt-0.5">Maksimal 2 MB</span>
                                <input type="file" accept="image/jpeg,image/png,image/webp" @change="handleKTAFile" class="hidden">
                            </label>
                            <template x-if="ktaPreview">
                                <div class="w-24 h-24 rounded-xl overflow-hidden border border-gray-200 dark:border-gray-700 shrink-0">
                                    <img :src="ktaPreview" alt="Preview KTA" class="w-full h-full object-cover">
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- 9. Upload Bukti Transfer -->
                    <div class="pt-2">
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-2">
                            9. Upload Bukti Transfer Pembayaran * (Maks. 2MB)
                        </label>
                        <div class="flex flex-col sm:flex-row items-center gap-4">
                            <label class="w-full sm:flex-1 flex flex-col items-center justify-center p-6 border-2 border-dashed border-gray-300 dark:border-gray-700 rounded-xl hover:border-copper-500 dark:hover:border-copper-500 transition cursor-pointer bg-gray-50/50 dark:bg-gray-900/50">
                                <i data-lucide="credit-card" class="w-6 h-6 text-gray-400 mb-2"></i>
                                <span class="text-xs font-semibold text-gray-700 dark:text-gray-300" x-text="buktiFileName || 'Pilih foto bukti transfer (JPG, PNG, WebP)'"></span>
                                <span class="text-[10px] text-gray-400 mt-0.5">Maksimal 2 MB</span>
                                <input type="file" accept="image/jpeg,image/png,image/webp" @change="handleBuktiFile" class="hidden">
                            </label>
                            <template x-if="buktiPreview">
                                <div class="w-24 h-24 rounded-xl overflow-hidden border border-gray-200 dark:border-gray-700 shrink-0">
                                    <img :src="buktiPreview" alt="Preview Bukti" class="w-full h-full object-cover">
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-4">
                        <button type="submit" :disabled="isSubmitting" 
                                class="w-full py-4 bg-copper-600 hover:bg-copper-700 disabled:opacity-50 text-white font-bold rounded-xl transition shadow-lg shadow-copper-600/25 flex items-center justify-center gap-2 text-sm">
                            <template x-if="isSubmitting">
                                <span class="flex items-center gap-2">
                                    <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                    </svg>
                                    Mengirim Pendaftaran...
                                </span>
                            </template>
                            <template x-if="!isSubmitting">
                                <span class="flex items-center gap-2">
                                    <i data-lucide="send" class="w-4 h-4"></i>
                                    Kirim Pendaftaran Sekarang
                                </span>
                            </template>
                        </button>
                    </div>
                </form>
            </div>
        </template>

    </div>
</div>

<script>
function registrationForm() {
    return {
        formData: {
            nama: '',
            email: '',
            telepon: '',
            pangkat: '',
            nrp: '',
            satuan: ''
        },
        selectedKategori: ['Pistol Presisi 20M'],
        ktaFile: null,
        ktaFileName: '',
        ktaPreview: null,
        buktiFile: null,
        buktiFileName: '',
        buktiPreview: null,
        isSubmitting: false,
        errorMessage: null,
        successData: null,

        get totalBiaya() {
            return this.selectedKategori.length * 200000;
        },

        toggleKategori(kat) {
            if (this.selectedKategori.includes(kat)) {
                if (this.selectedKategori.length === 1) return; // minimal 1 kategori
                this.selectedKategori = this.selectedKategori.filter(k => k !== kat);
            } else {
                this.selectedKategori.push(kat);
            }
        },

        handleKTAFile(e) {
            const file = e.target.files[0];
            if (!file) return;
            if (file.size > 2 * 1024 * 1024) {
                alert('Ukuran file KTA melebihi batas 2MB');
                return;
            }
            this.ktaFile = file;
            this.ktaFileName = file.name;
            this.ktaPreview = URL.createObjectURL(file);
        },

        handleBuktiFile(e) {
            const file = e.target.files[0];
            if (!file) return;
            if (file.size > 2 * 1024 * 1024) {
                alert('Ukuran file bukti transfer melebihi batas 2MB');
                return;
            }
            this.buktiFile = file;
            this.buktiFileName = file.name;
            this.buktiPreview = URL.createObjectURL(file);
        },

        formatRupiah(num) {
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(num);
        },

        async submitForm() {
            this.errorMessage = null;

            if (this.selectedKategori.length === 0) {
                this.errorMessage = 'Pilih minimal satu kategori lomba';
                return;
            }
            if (!this.ktaFile) {
                this.errorMessage = 'Upload foto KTA / Tanda Pengenal wajib dilampirkan';
                return;
            }
            if (!this.buktiFile) {
                this.errorMessage = 'Upload bukti transfer pembayaran wajib dilampirkan';
                return;
            }

            this.isSubmitting = true;

            const fd = new FormData();
            fd.append('nama', this.formData.nama);
            fd.append('email', this.formData.email);
            fd.append('telepon', this.formData.telepon);
            fd.append('pangkat', this.formData.pangkat);
            fd.append('nrp', this.formData.nrp);
            fd.append('satuan', this.formData.satuan);
            this.selectedKategori.forEach(k => fd.append('kategori[]', k));
            fd.append('foto_kta', this.ktaFile);
            fd.append('bukti_transfer', this.buktiFile);

            try {
                const res = await fetch('/api/register.php', {
                    method: 'POST',
                    body: fd
                });
                const data = await res.json();

                if (!res.ok || !data.success) {
                    throw new Error(data.error || 'Gagal menyimpan pendaftaran');
                }

                this.successData = data;
                window.scrollTo({ top: 0, behavior: 'smooth' });
                setTimeout(() => { if (typeof lucide !== 'undefined') lucide.createIcons(); }, 100);
            } catch (err) {
                this.errorMessage = err.message || 'Terjadi kesalahan sistem. Silakan coba kembali.';
            } finally {
                this.isSubmitting = false;
                setTimeout(() => { if (typeof lucide !== 'undefined') lucide.createIcons(); }, 100);
            }
        }
    }
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
