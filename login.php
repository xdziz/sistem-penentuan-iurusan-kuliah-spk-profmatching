<?php
session_start();
require_once '../config/database.php';
include '../includes/header.php';
?>

<style>
    .auth-shell {
        --auth-primary: var(--primary, #4F46E5);
        --auth-primary-dark: #3730A3;
        --auth-accent: #F59E0B;
        --auth-ink: #1E293B;
        --auth-muted: var(--text-muted, #64748B);
        --auth-border: var(--border, #E2E8F0);
        --auth-radius: var(--radius, 12px);

        display: grid;
        grid-template-columns: 1.05fr 1fr;
        min-height: calc(100vh - 2rem);
        max-width: 1100px;
        margin: 2rem auto;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 30px 60px -20px rgba(30, 41, 59, 0.18);
    }

    /* ---------- Left: guidance-path illustration ---------- */
    .auth-visual {
        position: relative;
        background: linear-gradient(155deg, var(--auth-primary-dark) 0%, var(--auth-primary) 55%, #6366F1 100%);
        padding: 3rem 2.5rem;
        color: #fff;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        overflow: hidden;
    }

    .auth-visual::before {
        content: "";
        position: absolute;
        inset: 0;
        background:
            radial-gradient(circle at 85% 15%, rgba(245, 158, 11, 0.25), transparent 45%),
            radial-gradient(circle at 10% 90%, rgba(255, 255, 255, 0.12), transparent 40%);
        pointer-events: none;
    }

    .auth-brand {
        display: flex;
        align-items: center;
        gap: 0.6rem;
        font-weight: 700;
        font-size: 1.05rem;
        letter-spacing: 0.01em;
        position: relative;
        z-index: 1;
    }

    .auth-brand-mark {
        width: 34px;
        height: 34px;
        border-radius: 10px;
        background: rgba(255, 255, 255, 0.16);
        display: grid;
        place-items: center;
        flex-shrink: 0;
    }

    .auth-path {
        position: relative;
        z-index: 1;
        flex: 1;
        display: flex;
        align-items: center;
        margin: 1.5rem 0;
    }

    .auth-path svg { width: 100%; height: auto; overflow: visible; }

    .auth-path .path-line {
        stroke-dasharray: 6 8;
        stroke-dashoffset: 400;
        animation: auth-draw 2.4s ease-out forwards;
    }

    .auth-path .path-node {
        opacity: 0;
        animation: auth-pop 0.5s ease-out forwards;
    }

    .auth-path .node-1 { animation-delay: 0.35s; }
    .auth-path .node-2 { animation-delay: 0.85s; }
    .auth-path .node-3 { animation-delay: 1.35s; }
    .auth-path .node-4 { animation-delay: 1.85s; }

    .auth-path .node-label {
        font: 600 11px system-ui, sans-serif;
        fill: rgba(255, 255, 255, 0.85);
        opacity: 0;
        animation: auth-pop 0.5s ease-out forwards;
    }
    .auth-path text.node-1 { animation-delay: 0.45s; }
    .auth-path text.node-2 { animation-delay: 0.95s; }
    .auth-path text.node-3 { animation-delay: 1.45s; }
    .auth-path text.node-4 { animation-delay: 1.95s; }

    @keyframes auth-draw { to { stroke-dashoffset: 0; } }
    @keyframes auth-pop {
        from { opacity: 0; transform: scale(0.4); }
        to { opacity: 1; transform: scale(1); }
    }

    .auth-quote {
        position: relative;
        z-index: 1;
        font-size: 0.95rem;
        line-height: 1.6;
        color: rgba(255, 255, 255, 0.92);
        max-width: 320px;
    }

    .auth-quote strong { display: block; font-size: 1.15rem; margin-bottom: 0.4rem; }

    /* ---------- Right: form panel ---------- */
    .auth-form-panel {
        background: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 3rem 2.5rem;
    }

    .auth-card { width: 100%; max-width: 360px; }

    .auth-header { text-align: center; margin-bottom: 1.75rem; }
    .auth-header h2 {
        font-size: 1.6rem;
        font-weight: 700;
        color: var(--auth-ink);
        margin-bottom: 0.4rem;
    }
    .auth-header p { color: var(--auth-muted); font-size: 0.9rem; }

    .auth-alert {
        display: flex;
        align-items: center;
        gap: 0.6rem;
        padding: 0.75rem 0.9rem;
        border-radius: var(--auth-radius);
        margin-bottom: 1.5rem;
        font-size: 0.85rem;
        animation: auth-slide-in 0.3s ease-out;
    }
    .auth-alert.error {
        background: #FEF2F2;
        color: var(--danger, #DC2626);
        border: 1px solid #FEE2E2;
    }
    .auth-alert.success {
        background: #ECFDF5;
        color: var(--success, #059669);
        border: 1px solid #D1FAE5;
    }
    @keyframes auth-slide-in {
        from { opacity: 0; transform: translateY(-6px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .auth-field { margin-bottom: 1.1rem; }
    .auth-field label {
        display: block;
        font-size: 0.82rem;
        font-weight: 600;
        color: var(--auth-ink);
        margin-bottom: 0.4rem;
    }

    .auth-input-group {
        position: relative;
        display: flex;
        align-items: center;
        border: 1.5px solid var(--auth-border);
        border-radius: var(--auth-radius);
        background: #F8FAFC;
        transition: border-color 0.15s ease, box-shadow 0.15s ease, background 0.15s ease;
    }
    .auth-input-group:focus-within {
        border-color: var(--auth-primary);
        background: #fff;
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.12);
    }
    .auth-input-group svg {
        flex-shrink: 0;
        margin-left: 0.85rem;
        color: var(--auth-muted);
    }
    .auth-input-group:focus-within svg { color: var(--auth-primary); }

    .auth-input-group input {
        width: 100%;
        border: none;
        background: transparent;
        outline: none;
        padding: 0.7rem 0.75rem;
        font-size: 0.92rem;
        color: var(--auth-ink);
    }

    .auth-toggle-pass {
        background: none;
        border: none;
        cursor: pointer;
        color: var(--auth-muted);
        padding: 0 0.85rem;
        display: flex;
        align-items: center;
    }
    .auth-toggle-pass:hover { color: var(--auth-primary); }

    .auth-submit {
        width: 100%;
        margin-top: 0.5rem;
        padding: 0.8rem;
        border: none;
        border-radius: var(--auth-radius);
        background: var(--auth-primary);
        color: #fff;
        font-weight: 600;
        font-size: 0.95rem;
        cursor: pointer;
        transition: transform 0.12s ease, box-shadow 0.12s ease, background 0.15s ease;
        box-shadow: 0 8px 18px -6px rgba(79, 70, 229, 0.45);
    }
    .auth-submit:hover { background: var(--auth-primary-dark); transform: translateY(-1px); }
    .auth-submit:active { transform: translateY(0); }
    .auth-submit:focus-visible { outline: 2px solid var(--auth-accent); outline-offset: 2px; }

    .auth-footer-link {
        text-align: center;
        margin-top: 1.75rem;
        padding-top: 1.4rem;
        border-top: 1px solid var(--auth-border);
        font-size: 0.85rem;
        color: var(--auth-muted);
    }
    .auth-footer-link a {
        color: var(--auth-primary);
        font-weight: 600;
        text-decoration: none;
    }
    .auth-footer-link a:hover { text-decoration: underline; }

    @media (max-width: 860px) {
        .auth-shell { grid-template-columns: 1fr; margin: 1rem; border-radius: 16px; }
        .auth-visual { display: none; }
        .auth-form-panel { padding: 2.5rem 1.5rem; }
    }

    @media (prefers-reduced-motion: reduce) {
        .auth-path .path-line,
        .auth-path .path-node,
        .auth-path .node-label,
        .auth-alert {
            animation: none !important;
            opacity: 1 !important;
            stroke-dashoffset: 0 !important;
        }
    }
</style>

<div class="auth-shell">

    <div class="auth-visual">
        <div class="auth-brand">
            <span class="auth-brand-mark">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 10v6M2 10l10-5 10 5-10 5-10-5z"></path>
                    <path d="M6 12v5c0 1.5 2.5 3 6 3s6-1.5 6-3v-5"></path>
                </svg>
            </span>
            Darul Hikmah
        </div>

        <div class="auth-path">
            <svg viewBox="0 0 320 220">
                <path class="path-line" d="M20 190 C 90 190, 80 100, 150 100 S 220 30, 300 30"
                      fill="none" stroke="rgba(255,255,255,0.55)" stroke-width="2.5"/>

                <circle class="path-node node-1" cx="20" cy="190" r="7" fill="#fff"/>
                <text class="node-label node-1" x="32" y="194">SMA</text>

                <circle class="path-node node-2" cx="150" cy="100" r="7" fill="#FCD34D"/>
                <text class="node-label node-2" x="162" y="104">Tes Minat</text>

                <circle class="path-node node-3" cx="225" cy="60" r="7" fill="#fff"/>
                <text class="node-label node-3" x="237" y="64">Jurusan</text>

                <circle class="path-node node-4" cx="300" cy="30" r="9" fill="#F59E0B"/>
                <text class="node-label node-4" x="285" y="14">Karier</text>
            </svg>
        </div>

        <p class="auth-quote">
            <strong>Satu langkah lagi.</strong>
            Masuk untuk melanjutkan perjalanan menemukan jurusan yang paling cocok untukmu.
        </p>
    </div>

    <div class="auth-form-panel">
        <div class="auth-card">
            <div class="auth-header">
                <h2>Selamat Datang Kembali</h2>
                <p>Silakan masuk untuk melanjutkan</p>
            </div>

            <?php if (isset($_GET['error'])): ?>
                <div class="auth-alert error">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                    <span>Email/NIS atau password salah.</span>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['register']) && $_GET['register'] === 'success'): ?>
                <div class="auth-alert success">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                    <span>Registrasi berhasil! Silakan login.</span>
                </div>
            <?php endif; ?>

            <form action="../proses/proses_login.php" method="POST">
                <div class="auth-field">
                    <label for="email">Email atau NIS</label>
                    <div class="auth-input-group">
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                        <input type="text" name="email" id="email" placeholder="Contoh: admin atau siswa@email.com" required>
                    </div>
                </div>

                <div class="auth-field">
                    <label for="password">Password</label>
                    <div class="auth-input-group">
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0110 0v4"></path></svg>
                        <input type="password" name="password" id="password" placeholder="••••••••" required>
                        <button type="button" class="auth-toggle-pass" id="togglePassword" aria-label="Tampilkan password">
                            <svg id="eyeIcon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                        </button>
                    </div>
                </div>

                <button type="submit" class="auth-submit">Masuk Sekarang</button>
            </form>

            <div class="auth-footer-link">
                <p>Belum punya akun? <a href="register.php">Daftar Akun Baru</a></p>
            </div>
        </div>
    </div>
</div>

<script>
    (function () {
        var toggle = document.getElementById('togglePassword');
        var pass = document.getElementById('password');
        var eye = document.getElementById('eyeIcon');
        if (!toggle || !pass || !eye) return;

        toggle.addEventListener('click', function () {
            var show = pass.type === 'password';
            pass.type = show ? 'text' : 'password';
            toggle.setAttribute('aria-label', show ? 'Sembunyikan password' : 'Tampilkan password');
            eye.innerHTML = show
                ? '<path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line>'
                : '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle>';
        });
    })();
</script>

<?php include '../includes/footer.php'; ?>