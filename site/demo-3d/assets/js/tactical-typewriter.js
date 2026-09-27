/**
 * BDA Shooting Championship 2026
 * Tactical Terminal Typewriter Engine (Command Prompt Motion Graphics)
 *
 * Efek:
 * - Mengetik teks huruf demi huruf secara berurutan seperti terminal prompt militer (HUD / Command Prompt).
 * - Dilengkapi kursor terminal berkedip (amber/copper block cursor '▌').
 * - Preserving Layout (Zero CLS): Seluruh teks tetap berada pada posisi dan ukuran aslinya
 *   dengan opacity: 0 lalu diungkap menjadi opacity: 1, mencegah layout shift pada halaman.
 * - Mendukung format teks kaya (HTML tags seperti <strong>, <em>, dll tetap utuh).
 * - Re-trigger Otomatis: Saat scroll keluar layar lalu masuk kembali (scroll up / scroll down),
 *   animasi ketikan terminal akan dimainkan ulang secara otomatis.
 */

(function () {
    'use strict';

    // Inject CSS untuk animasi blink kursor terminal jika belum ada
    if (!document.getElementById('terminal-typewriter-styles')) {
        const style = document.createElement('style');
        style.id = 'terminal-typewriter-styles';
        style.textContent = `
            @keyframes terminalCursorBlink {
                0%, 100% { opacity: 1; }
                50% { opacity: 0; }
            }
            .terminal-cursor {
                display: inline-block;
                color: #f59e0b;
                font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
                font-weight: 700;
                line-height: 1;
                margin-left: 1px;
                user-select: none;
                animation: terminalCursorBlink 0.65s step-end infinite;
                vertical-align: baseline;
            }
            .type-char {
                transition: opacity 0.03s ease-out;
            }
        `;
        document.head.appendChild(style);
    }

    class TacticalTypewriterElement {
        constructor(el) {
            this.el = el;
            this.rawHTML = el.innerHTML;
            this.fullText = el.innerText.trim();

            // Set aria-label untuk aksesibilitas screen reader
            if (!el.getAttribute('aria-label')) {
                el.setAttribute('aria-label', this.fullText);
            }

            this.charSpans = [];
            this.cursor = null;
            this.timerId = null;
            this.blinkTimer = null;
            this.isRunning = false;
            this.hasStarted = false;
            this.hasExited = false;
            this.currentIndex = 0;
            this.lastStartTime = 0;

            this.prepareDOM();
        }

        prepareDOM() {
            // Rekursif ambil text nodes
            const textNodes = [];
            const walk = (node) => {
                if (node.nodeType === Node.TEXT_NODE) {
                    if (node.textContent.length > 0) {
                        textNodes.push(node);
                    }
                } else if (node.nodeType === Node.ELEMENT_NODE) {
                    if (!node.classList.contains('terminal-cursor')) {
                        Array.from(node.childNodes).forEach(walk);
                    }
                }
            };
            walk(this.el);

            const charSpans = [];
            textNodes.forEach(textNode => {
                const text = textNode.textContent;
                const fragment = document.createDocumentFragment();

                for (let i = 0; i < text.length; i++) {
                    const char = text[i];
                    const span = document.createElement('span');
                    span.className = 'type-char';
                    span.style.opacity = '0';
                    span.textContent = char;
                    fragment.appendChild(span);
                    charSpans.push(span);
                }

                if (textNode.parentNode) {
                    textNode.parentNode.replaceChild(fragment, textNode);
                }
            });

            this.charSpans = charSpans;

            // Buat kursor terminal
            const cursor = document.createElement('span');
            cursor.className = 'terminal-cursor';
            cursor.textContent = '▌';
            cursor.setAttribute('aria-hidden', 'true');
            this.cursor = cursor;

            if (this.charSpans.length > 0) {
                this.charSpans[0].parentNode.insertBefore(this.cursor, this.charSpans[0]);
            } else {
                this.el.appendChild(this.cursor);
            }

            // Hitung kecepatan ketik dinamis (10ms - 22ms per huruf)
            this.speed = Math.max(10, Math.min(22, Math.round(1600 / Math.max(this.charSpans.length, 1))));
        }

        start() {
            if (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
                // Fallback jika mode reduced motion aktif
                this.revealAll();
                if (this.cursor) this.cursor.remove();
                return;
            }

            const now = performance.now();
            if (this.lastStartTime && (now - this.lastStartTime < 400)) {
                return;
            }
            this.lastStartTime = now;

            this.stop();
            this.reset();

            this.isRunning = true;
            this.hasStarted = true;

            const step = () => {
                if (!this.isRunning) return;

                if (this.currentIndex < this.charSpans.length) {
                    const currentSpan = this.charSpans[this.currentIndex];
                    currentSpan.style.opacity = '1';

                    // Pindahkan kursor ke setelah karakter saat ini
                    if (this.cursor && currentSpan.parentNode) {
                        currentSpan.parentNode.insertBefore(this.cursor, currentSpan.nextSibling);
                    }

                    this.currentIndex++;
                    this.timerId = setTimeout(step, this.speed);
                } else {
                    this.finish();
                }
            };

            this.timerId = setTimeout(step, this.speed);
        }

        stop() {
            this.isRunning = false;
            if (this.timerId) {
                clearTimeout(this.timerId);
                this.timerId = null;
            }
            if (this.blinkTimer) {
                clearTimeout(this.blinkTimer);
                this.blinkTimer = null;
            }
        }

        reset() {
            this.stop();
            this.currentIndex = 0;
            for (let i = 0; i < this.charSpans.length; i++) {
                this.charSpans[i].style.opacity = '0';
            }
            if (this.cursor) {
                this.cursor.style.opacity = '1';
                if (this.charSpans.length > 0 && this.charSpans[0].parentNode) {
                    this.charSpans[0].parentNode.insertBefore(this.cursor, this.charSpans[0]);
                }
            }
        }

        revealAll() {
            this.stop();
            for (let i = 0; i < this.charSpans.length; i++) {
                this.charSpans[i].style.opacity = '1';
            }
        }

        finish() {
            this.isRunning = false;
            this.revealAll();

            // Kursor berkedip sebentar lalu menghilang dengan halus
            if (this.cursor) {
                this.blinkTimer = setTimeout(() => {
                    if (this.cursor) {
                        this.cursor.style.transition = 'opacity 0.4s ease-out';
                        this.cursor.style.opacity = '0';
                    }
                }, 1200);
            }

            this.el.dispatchEvent(new CustomEvent('typewriter:complete', { bubbles: true }));
        }
    }

    // Manager & IntersectionObserver
    const TacticalTypewriter = {
        instances: new Map(),

        init() {
            if (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
                return;
            }

            const elements = document.querySelectorAll('[data-typewriter]');
            if (!elements.length) return;

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    const el = entry.target;
                    let instance = this.instances.get(el);
                    if (!instance) {
                        instance = new TacticalTypewriterElement(el);
                        this.instances.set(el, instance);
                    }

                    if (entry.isIntersecting) {
                        // Elemen muncul di layar
                        if (!instance.hasStarted || instance.hasExited) {
                            instance.hasExited = false;
                            instance.start();
                        }
                    } else {
                        // Elemen keluar dari layar (tidak terlihat)
                        instance.hasExited = true;
                        if (instance.isRunning) {
                            instance.stop();
                            instance.revealAll();
                        }
                    }
                });
            }, {
                threshold: 0.15,
                rootMargin: '0px'
            });

            elements.forEach(el => {
                let instance = new TacticalTypewriterElement(el);
                this.instances.set(el, instance);
                observer.observe(el);
            });
        },

        type(el) {
            if (!el) return;
            let instance = this.instances.get(el);
            if (!instance) {
                instance = new TacticalTypewriterElement(el);
                this.instances.set(el, instance);
            }
            instance.start();
        },

        replayAll() {
            const elements = document.querySelectorAll('[data-typewriter]');
            elements.forEach(el => this.type(el));
        }
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => TacticalTypewriter.init());
    } else {
        TacticalTypewriter.init();
    }

    window.TacticalTypewriter = TacticalTypewriter;
})();
