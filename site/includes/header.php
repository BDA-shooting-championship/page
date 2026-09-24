<?php
require_once __DIR__ . '/config.php';
$pageTitle = $pageTitle ?? EVENT_NAME;
$currentPage = $currentPage ?? '';
?>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <meta name="description" content="<?= EVENT_NAME ?> — <?= EVENT_DATE ?> di <?= EVENT_LOCATION ?>">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        copper: { 50:'#fdf5ef', 100:'#fae6d4', 200:'#f4caa8', 300:'#eca86f', 400:'#e48840', 500:'#db6d20', 600:'#c35516', 700:'#a24115', 800:'#823619', 900:'#6a2f17' },
                        bronze: { 50:'#f9f5f0', 100:'#f0e6d5', 200:'#e0ccaa', 300:'#ccab78', 400:'#ba8f54', 500:'#a87a3c', 600:'#8f6230', 700:'#754d29', 800:'#624027', 900:'#533824' },
                    },
                    fontFamily: {
                        display: ['Oswald', 'sans-serif'],
                        body: ['Inter', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Oswald:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    
    <!-- Dark Mode Init -->
    <script>
        if (localStorage.getItem('theme') === 'dark' || (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }
    </script>
    
    <style>
        body { font-family: 'Inter', sans-serif; }
        .font-display { font-family: 'Oswald', sans-serif; }
        .target-pattern { 
            background-image: radial-gradient(circle, rgba(219,109,32,0.05) 1px, transparent 1px);
            background-size: 30px 30px;
        }
        .hero-gradient {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
        }
        .glass { background: rgba(255,255,255,0.05); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.1); }
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        .animate-fade-in-up { animation: fadeInUp 0.6s ease-out forwards; }
    </style>
</head>
<body class="bg-white dark:bg-gray-950 text-gray-900 dark:text-gray-100 font-body" x-data="{ mobileMenu: false }">

<!-- Navbar -->
<nav class="fixed top-0 w-full z-50 transition-all duration-300 bg-white/90 dark:bg-gray-950/90 backdrop-blur-md border-b border-gray-200 dark:border-gray-800" x-data="{ scrolled: false }" @scroll.window="scrolled = window.scrollY > 50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <!-- Logo -->
            <a href="/" class="flex items-center gap-3">
                <img src="/assets/logo-championship.jpeg" alt="Logo" class="h-10 w-10 rounded-full object-cover" onerror="this.style.display='none'">
                <span class="font-display font-bold text-lg text-copper-600 dark:text-copper-400">BSC 2026</span>
            </a>
            
            <!-- Desktop Menu -->
            <div class="hidden md:flex items-center gap-6">
                <a href="/" class="text-sm font-medium hover:text-copper-600 dark:hover:text-copper-400 transition <?= $currentPage === 'home' ? 'text-copper-600 dark:text-copper-400 font-semibold' : '' ?>">Beranda</a>
                <a href="/daftar.php" class="text-sm font-medium hover:text-copper-600 dark:hover:text-copper-400 transition <?= $currentPage === 'daftar' ? 'text-copper-600 dark:text-copper-400 font-semibold' : '' ?>">Pendaftaran</a>
                <a href="/live-score.php" class="text-sm font-medium hover:text-copper-600 dark:hover:text-copper-400 transition <?= $currentPage === 'live-score' ? 'text-copper-600 dark:text-copper-400 font-semibold' : '' ?>">Live Score</a>
                <a href="/admin/index.php" class="text-xs px-2.5 py-1 rounded-md border border-gray-300 dark:border-gray-700 hover:border-copper-500 font-semibold transition <?= $currentPage === 'admin' ? 'text-copper-600 border-copper-600' : 'text-gray-500 hover:text-gray-900 dark:hover:text-white' ?>">Admin</a>
                <!-- Dark Mode Toggle -->
                <button @click="document.documentElement.classList.toggle('dark'); localStorage.setItem('theme', document.documentElement.classList.contains('dark') ? 'dark' : 'light')" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition" aria-label="Toggle Theme">
                    <i data-lucide="sun" class="w-5 h-5 hidden dark:block"></i>
                    <i data-lucide="moon" class="w-5 h-5 block dark:hidden"></i>
                </button>
            </div>
            
            <!-- Mobile Toggle -->
            <button @click="mobileMenu = !mobileMenu" class="md:hidden p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800">
                <i data-lucide="menu" class="w-6 h-6" x-show="!mobileMenu"></i>
                <i data-lucide="x" class="w-6 h-6" x-show="mobileMenu" x-cloak></i>
            </button>
        </div>
    </div>
    
    <!-- Mobile Menu -->
    <div x-show="mobileMenu" x-transition class="md:hidden border-t border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-950">
        <div class="px-4 py-3 space-y-2">
            <a href="/" class="block py-2 text-sm font-medium hover:text-copper-600">Beranda</a>
            <a href="/daftar.php" class="block py-2 text-sm font-medium hover:text-copper-600">Pendaftaran</a>
            <a href="/live-score.php" class="block py-2 text-sm font-medium hover:text-copper-600">Live Score</a>
            <a href="/admin/index.php" class="block py-2 text-sm font-medium hover:text-copper-600">Admin Dashboard</a>
            <button @click="document.documentElement.classList.toggle('dark'); localStorage.setItem('theme', document.documentElement.classList.contains('dark') ? 'dark' : 'light')" class="py-2 text-sm font-medium hover:text-copper-600">Toggle Dark Mode</button>
        </div>
    </div>
</nav>

<!-- Spacer for fixed navbar -->
<div class="h-16"></div>
