/**
 * BDA Shooting Championship 2026
 * Tactical Text Scramble & Sequential Decoder Engine (Per-Kata / Word-Level)
 *
 * Efek: 
 * - Seluruh kalimat yang di-scramble menggunakan HURUF KAPITAL (Uppercase).
 * - Karakter acak HANYA menggunakan HURUF KAPITAL A-Z (pure uppercase letters).
 * - Warna karakter scrambled SAMA dengan warna normal kata tersebut (inherit alami tanpa pewarnaan buatan).
 * - Scramble berjalan per kata secara paralel/independen (bukan per baris/kalimat) sehingga cepat dan tidak menunggu lama.
 * - Berhenti berurutan per huruf dari kata membentuk kata (left-to-right sequential per-word).
 * - Re-trigger Otomatis: Jika kata/judul scroll keluar layar lalu muncul kembali (scroll up / scroll down),
 *   animasi scramble akan dimainkan ulang secara otomatis.
 */

(function () {
    'use strict';

    // Karakter acak HANYA huruf kapital A-Z
    const CAPITAL_LETTERS = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

    class TacticalScrambleElement {
        constructor(el, options = {}) {
            this.el = el;
            
            // Pastikan seluruh teks target menggunakan HURUF KAPITAL (Uppercase)
            const baseText = el.getAttribute('data-scramble-text') || (el.textContent ? el.textContent.trim() : '') || (el.innerText ? el.innerText.trim() : '');
            this.rawText = baseText.toUpperCase();
            if (this.rawText) {
                el.setAttribute('data-scramble-text', this.rawText);
            }
            el.classList.add('uppercase');

            // Simpan atribut aksesibilitas untuk screen reader
            if (!el.getAttribute('aria-label')) {
                el.setAttribute('aria-label', this.rawText);
            }

            this.options = Object.assign({
                chars: CAPITAL_LETTERS,
                initialScrambleFrames: 5,  // frame acak cepat sebelum huruf pertama tiap kata mengunci (~140ms)
                staggerFrames: 1.6,        // jeda frame antar huruf yang mengunci dalam satu kata (~45ms)
                fpsInterval: 1000 / 35,    // kecepatan flip glif acak (~35fps)
            }, options);

            this.isRunning = false;
            this.hasStarted = false;
            this.hasExited = false;
            this.lastStartTime = 0;
            this.rafId = null;
        }

        start() {
            if (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
                // Fallback aksesibilitas OS jika pengguna memilih tanpa animasi
                this.el.innerText = this.rawText;
                return;
            }

            const now = performance.now();
            if (this.lastStartTime && (now - this.lastStartTime < 350)) {
                return;
            }
            this.lastStartTime = now;

            if (this.isRunning) {
                if (this.rafId) {
                    cancelAnimationFrame(this.rafId);
                    this.rafId = null;
                }
                this.isRunning = false;
            }

            this.isRunning = true;
            this.hasStarted = true;

            // Pisahkan teks menjadi kata-kata dan pertahankan spasi
            const chunks = this.rawText.split(/(\s+)/);
            this.el.innerHTML = '';
            this.el.setAttribute('aria-hidden', 'true');

            const charEntries = [];

            chunks.forEach((chunk) => {
                if (/^\s+$/.test(chunk)) {
                    // Chunk adalah spasi / newline asli
                    const spaceNode = document.createTextNode(chunk);
                    this.el.appendChild(spaceNode);
                } else {
                    // Chunk adalah kata
                    const wordSpan = document.createElement('span');
                    wordSpan.style.display = 'inline-block';
                    wordSpan.style.whiteSpace = 'nowrap';
                    wordSpan.className = 'scramble-word';

                    // Beri offset acak halus per kata (0 - 2.5 frame) agar ritme antar kata lebih organik & random
                    const wordRandomJitter = Math.random() * 2.5;

                    const charsInWord = Array.from(chunk);
                    charsInWord.forEach((rawChar, charIndexInWord) => {
                        const targetChar = rawChar.toUpperCase();
                        const charSpan = document.createElement('span');
                        charSpan.className = 'scramble-char inline-block';
                        charSpan.textContent = targetChar;

                        wordSpan.appendChild(charSpan);

                        const isAlphaNum = /[A-Z0-9]/.test(targetChar);

                        // Kunci per kata: indeks charIndexInWord dihitung dari huruf pertama KATA TERSEBUT
                        // sehingga seluruh kata mengacak bersama dan selesai cepat dalam hitungan ratusan milidetik
                        const lockFrame = isAlphaNum
                            ? Math.round(this.options.initialScrambleFrames + wordRandomJitter + (charIndexInWord * this.options.staggerFrames))
                            : 0; // Tanda baca langsung terkunci

                        charEntries.push({
                            charSpan,
                            targetChar,
                            isAlphaNum,
                            resolved: !isAlphaNum,
                            lockFrame,
                        });
                    });

                    this.el.appendChild(wordSpan);

                    // Kunci min-width kata berdasarkan ukuran render aktual aslinya (Zero CLS)
                    const renderedWidth = wordSpan.getBoundingClientRect().width;
                    if (renderedWidth > 0) {
                        wordSpan.style.minWidth = `${renderedWidth}px`;
                    }

                    // Segera acak karakter huruf kapital dari frame 0
                    charsInWord.forEach((_, charIndexInWord) => {
                        const entry = charEntries[charEntries.length - charsInWord.length + charIndexInWord];
                        if (entry && entry.isAlphaNum) {
                            entry.charSpan.textContent = this.options.chars[Math.floor(Math.random() * this.options.chars.length)];
                        }
                    });
                }
            });

            this.charEntries = charEntries;
            this.totalFrames = Math.max(...charEntries.map(c => c.lockFrame), 8) + 4;
            this.frame = 0;
            this.lastTimestamp = performance.now();

            this.animate();
        }

        animate() {
            const loop = (currentTimestamp) => {
                if (!this.isRunning) return;

                const delta = currentTimestamp - this.lastTimestamp;
                if (delta >= this.options.fpsInterval) {
                    this.lastTimestamp = currentTimestamp;
                    this.frame++;

                    let allResolved = true;

                    for (let i = 0; i < this.charEntries.length; i++) {
                        const entry = this.charEntries[i];

                        if (!entry.isAlphaNum) continue;

                        if (this.frame >= entry.lockFrame) {
                            if (!entry.resolved) {
                                entry.resolved = true;
                                entry.charSpan.textContent = entry.targetChar;
                            }
                        } else {
                            // Fase acak cepat: pilih karakter random murni HANYA DARI HURUF KAPITAL (A-Z)
                            allResolved = false;
                            const randomGlyph = this.options.chars[Math.floor(Math.random() * this.options.chars.length)];
                            entry.charSpan.textContent = randomGlyph;
                        }
                    }

                    if (allResolved || this.frame >= this.totalFrames) {
                        this.finish();
                        return;
                    }
                }

                this.rafId = requestAnimationFrame(loop);
            };

            this.rafId = requestAnimationFrame(loop);
        }

        finish() {
            this.isRunning = false;
            if (this.rafId) {
                cancelAnimationFrame(this.rafId);
                this.rafId = null;
            }
            // Pulihkan teks final bersih dalam huruf kapital pada elemen
            this.el.innerText = this.rawText;
            this.el.removeAttribute('aria-hidden');
            this.el.dispatchEvent(new CustomEvent('scramble:complete', { bubbles: true }));
        }
    }

    // Engine Manager & IntersectionObserver
    const TacticalScramble = {
        instances: new Map(),

        init() {
            if (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
                return;
            }

            const elements = document.querySelectorAll('[data-scramble]');
            if (!elements.length) return;

            // Format seluruh teks elemen target langsung menjadi UPPERCASE
            elements.forEach(el => {
                const text = (el.getAttribute('data-scramble-text') || (el.textContent ? el.textContent.trim() : '') || (el.innerText ? el.innerText.trim() : '')).toUpperCase();
                if (text) {
                    el.setAttribute('data-scramble-text', text);
                    el.classList.add('uppercase');
                    if (el.children.length === 0) {
                        el.innerText = text;
                    }
                }
            });

            // IntersectionObserver: mendeteksi elemen saat masuk dan keluar layar
            // Saat scroll keluar (tidak muncul di layar), hasExited = true
            // Saat scroll masuk kembali (scroll up / scroll down), scramble dimainkan lagi!
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    const el = entry.target;
                    let instance = this.instances.get(el);
                    if (!instance) {
                        instance = new TacticalScrambleElement(el);
                        this.instances.set(el, instance);
                    }

                    if (entry.isIntersecting) {
                        // Elemen muncul di layar
                        if (!instance.hasStarted || instance.hasExited) {
                            instance.hasExited = false;
                            instance.start();
                        }
                    } else {
                        // Elemen keluar dari layar (tidak muncul di layar)
                        instance.hasExited = true;
                        if (instance.isRunning) {
                            instance.finish();
                        }
                    }
                });
            }, {
                threshold: 0.1,
                rootMargin: '0px'
            });

            elements.forEach(el => observer.observe(el));
        },

        scramble(el) {
            if (!el) return;
            let instance = this.instances.get(el);
            if (!instance) {
                instance = new TacticalScrambleElement(el);
                this.instances.set(el, instance);
            }
            instance.start();
        },

        replayAll() {
            const elements = document.querySelectorAll('[data-scramble]');
            elements.forEach(el => this.scramble(el));
        }
    };

    // Auto initialize on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => TacticalScramble.init());
    } else {
        TacticalScramble.init();
    }

    // Export to window
    window.TacticalScramble = TacticalScramble;
})();
