<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= esc($pageTitle ?? 'NutriPlan') ?></title>
  <style>
    :root {
      --bg: #edf5ef;
      --surface: #ffffff;
      --surface-2: #f7fbf8;
      --border: rgba(18, 56, 35, 0.12);
      --text: #123823;
      --muted: #5b7464;
      --brand: #1a6b45;
      --brand-dark: #155339;
      --shadow: 0 22px 50px rgba(14, 52, 31, 0.14);
      --radius-lg: 28px;
      --radius-md: 18px;
    }

    * { box-sizing: border-box; }
    body {
      margin: 0;
      min-height: 100vh;
      font-family: Inter, 'Segoe UI', Arial, sans-serif;
      color: var(--text);
      background:
        radial-gradient(circle at top left, rgba(26, 107, 69, 0.16), transparent 28%),
        radial-gradient(circle at bottom right, rgba(238, 207, 108, 0.18), transparent 24%),
        var(--bg);
    }

    a { color: inherit; text-decoration: none; }

    .auth-shell {
      min-height: 100vh;
      display: grid;
      grid-template-columns: 1.05fr 1fr;
    }

    .auth-hero {
      padding: 44px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      position: relative;
      overflow: hidden;
      background: linear-gradient(160deg, #0f4f31, #1a6b45 60%, #215f43);
    }

    .auth-hero::before,
    .auth-hero::after {
      content: '';
      position: absolute;
      border-radius: 999px;
      background: rgba(255, 255, 255, 0.08);
      filter: blur(2px);
    }

    .auth-hero::before { width: 260px; height: 260px; right: -90px; top: -80px; }
    .auth-hero::after { width: 180px; height: 180px; left: -60px; bottom: -40px; }

    .hero-card {
      width: min(100%, 420px);
      position: relative;
      z-index: 1;
    }

    .brand {
      display: inline-flex;
      align-items: center;
      gap: 12px;
      font-size: 32px;
      font-weight: 800;
      letter-spacing: -0.04em;
      margin-bottom: 16px;
    }

    .brand-badge {
      width: 54px;
      height: 54px;
      border-radius: 18px;
      display: grid;
      place-items: center;
      background: rgba(255, 255, 255, 0.14);
      backdrop-filter: blur(8px);
      font-size: 24px;
    }

    .hero-copy {
      font-size: 16px;
      line-height: 1.7;
      color: rgba(255, 255, 255, 0.82);
      max-width: 360px;
      margin-bottom: 28px;
    }

    .hero-metric {
      padding: 18px 20px;
      border-radius: var(--radius-md);
      background: rgba(255, 255, 255, 0.12);
      border: 1px solid rgba(255, 255, 255, 0.1);
      backdrop-filter: blur(6px);
      max-width: 320px;
    }

    .hero-metric strong { display: block; font-size: 34px; line-height: 1; margin-bottom: 4px; }

    .auth-content {
      padding: 30px;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .auth-card {
      width: min(100%, 480px);
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: var(--radius-lg);
      box-shadow: var(--shadow);
      padding: 34px;
    }

    .card-kicker {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 7px 12px;
      border-radius: 999px;
      background: var(--surface-2);
      color: var(--brand);
      font-size: 12px;
      font-weight: 700;
      margin-bottom: 14px;
    }

    .auth-title { margin: 0 0 8px; font-size: 30px; letter-spacing: -0.03em; }
    .auth-subtitle { margin: 0 0 26px; color: var(--muted); line-height: 1.6; }

    .flash {
      padding: 12px 14px;
      border-radius: 16px;
      margin-bottom: 18px;
      font-size: 14px;
      line-height: 1.5;
    }

    .flash.error { background: #fdecec; color: #a02e2e; }
    .flash.success { background: #e9f7ef; color: #155339; }

    <?= $this->renderSection('styles') ?>

    @media (max-width: 980px) {
      .auth-shell { grid-template-columns: 1fr; }
      .auth-hero { min-height: 280px; }
      .auth-content { padding-top: 0; }
    }

    @media (max-width: 640px) {
      .auth-hero, .auth-content { padding: 22px; }
      .auth-card { padding: 24px; border-radius: 22px; }
      .auth-title { font-size: 26px; }
    }
  </style>
</head>
<body>
  <div class="auth-shell">
    <section class="auth-hero">
      <div class="hero-card">
        <div class="brand"><span class="brand-badge">🥗</span> NutriPlan</div>
        <p class="hero-copy">Votre espace nutritionnel centralise la connexion, l'inscription et le suivi de vos régimes avec une interface plus lisible et cohérente.</p>
        <div class="hero-metric">
          <strong>IMC</strong>
          <span>Calcul dynamique lié à votre profil et à vos objectifs.</span>
        </div>
      </div>
    </section>
    <main class="auth-content">
      <div class="auth-card">
        <?php if (is_string($flashError = session()->getFlashdata('authError') ?? null)) : ?>
          <div class="flash error"><?= esc($flashError) ?></div>
        <?php endif; ?>
        <?php if (is_string($flashSuccess = session()->getFlashdata('authSuccess') ?? null)) : ?>
          <div class="flash success"><?= esc($flashSuccess) ?></div>
        <?php endif; ?>
        <?= $this->renderSection('content') ?>
      </div>
    </main>
  </div>
</body>
</html>