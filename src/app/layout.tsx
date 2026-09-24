import type { Metadata } from 'next';
import { Inter, Oswald } from 'next/font/google';
import './globals.css';
import Navbar from '@/components/Navbar';
import Footer from '@/components/Footer';

const inter = Inter({
  subsets: ['latin'],
  variable: '--font-sans',
  display: 'swap',
});

const oswald = Oswald({
  subsets: ['latin'],
  variable: '--font-heading',
  display: 'swap',
});

export const metadata: Metadata = {
  title: 'BDA Shooting Championship 2026 — Resimen I Pasukan Pelopor',
  description: 'Website Resmi Kejuaraan Menembak Pistol Presisi 20 Meter & Dueling Plat dalam rangka memperingati Anniversary Letting BDA 750 ke-7 di Resimen I Pasukan Pelopor, Kedung Halang, Bogor.',
  keywords: ['BDA Shooting Championship', 'BDA 750', 'Resimen I Pasukan Pelopor', 'Lomba Menembak Brimob', 'Pistol Presisi 20M', 'Dueling Plat'],
  authors: [{ name: 'Panitia BDA Shooting Championship 2026' }],
  icons: {
    icon: '/favicon.ico',
  },
};

export default function RootLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  return (
    <html lang="id" className={`${inter.variable} ${oswald.variable}`}>
      <head>
        <script
          dangerouslySetInnerHTML={{
            __html: `
              try {
                if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                  document.documentElement.classList.add('dark');
                } else {
                  document.documentElement.classList.remove('dark');
                }
              } catch (_) {}
            `,
          }}
        />
      </head>
      <body className="min-h-screen flex flex-col font-sans">
        <Navbar />
        <main className="flex-1">
          {children}
        </main>
        <Footer />
      </body>
    </html>
  );
}
