<!-- Footer -->
<footer class="bg-gray-900 dark:bg-gray-950 text-gray-300 border-t border-gray-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <!-- Brand -->
            <div class="md:col-span-1">
                <div class="flex items-center gap-2.5 mb-4">
                    <img src="/assets/logo-bda.png" alt="Logo BDA 750" class="h-10 w-10 object-contain">
                    <img src="/assets/logo-championship.png" alt="Logo BSC 2026" class="h-10 w-10 object-contain">
                    <span class="font-display font-bold text-lg text-copper-400">BSC 2026</span>
                </div>
                <p class="text-sm text-gray-400">Kejuaraan Menembak Pistol Presisi 20M & Dueling Plat</p>
                <p class="text-sm text-gray-500 mt-2">17 — 18 Oktober 2026</p>
            </div>
            
            <!-- Navigation -->
            <div>
                <h4 class="font-display font-semibold text-white mb-4">Navigasi</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="/" class="hover:text-copper-400 transition">Beranda</a></li>
                    <li><a href="/daftar.php" class="hover:text-copper-400 transition">Pendaftaran</a></li>
                    <li><a href="/live-score.php" class="hover:text-copper-400 transition">Live Score</a></li>
                    <li><a href="/admin/" class="hover:text-copper-400 transition">Admin</a></li>
                </ul>
            </div>
            
            <!-- Location -->
            <div>
                <h4 class="font-display font-semibold text-white mb-4">Lokasi</h4>
                <p class="text-sm text-gray-400">Lapangan Tembak Shooting House</p>
                <p class="text-sm text-gray-400">Resimen I Pasukan Pelopor</p>
                <p class="text-sm text-gray-400">Kedung Halang, Bogor</p>
            </div>
            
            <!-- Contact -->
            <div>
                <h4 class="font-display font-semibold text-white mb-4">Kontak</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="https://wa.me/6285283525761" target="_blank" class="hover:text-copper-400 transition flex items-center gap-2"><i data-lucide="phone" class="w-4 h-4"></i>Briptu Ady — 0852-8352-5761</a></li>
                    <li><a href="https://wa.me/6285272377704" target="_blank" class="hover:text-copper-400 transition flex items-center gap-2"><i data-lucide="phone" class="w-4 h-4"></i>Briptu Huges — 0852-7237-7704</a></li>
                </ul>
            </div>
        </div>
        
        <div class="mt-10 pt-6 border-t border-gray-800 text-center text-sm text-gray-500">
            &copy; 2026 BDA Shooting Championship. All rights reserved.
        </div>
    </div>
</footer>

<!-- Init Lucide Icons -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    });
    // Re-init after Alpine.js updates DOM
    document.addEventListener('alpine:initialized', function() {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    });
</script>
</body>
</html>
