<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= esc($pageTitle ?? 'NutriPlan') ?></title>
  <style>
    :root {
      --bg: #eef6f0;
      --surface: #ffffff;
      --surface-2: #f7fbf8;
      --border: rgba(18, 56, 35, 0.12);
      --text: #123823;
      --muted: #5f7667;
      --brand: #1a6b45;
      --brand-dark: #145438;
      --brand-soft: #e7f3ec;
      --gold: #f0c040;
      --gold-dark: #c98a11;
      --shadow: 0 20px 44px rgba(14, 52, 31, 0.09);
      --radius-lg: 24px;
      --radius-md: 16px;
    }

    * { box-sizing: border-box; }
    body {
      margin: 0;
      min-height: 100vh;
      font-family: Inter, 'Segoe UI', Arial, sans-serif;
      color: var(--text);
      background:
        radial-gradient(circle at top left, rgba(26, 107, 69, 0.12), transparent 24%),
        radial-gradient(circle at bottom right, rgba(240, 192, 64, 0.12), transparent 22%),
        var(--bg);
    }

    a { color: inherit; text-decoration: none; }

    .app-shell {
      min-height: 100vh;
      display: grid;
      grid-template-columns: 280px minmax(0, 1fr);
    }

    .sidebar {
      background: linear-gradient(180deg, #0f4f31, #1a6b45 62%, #18563b);
      color: white;
      padding: 28px 20px 20px;
      display: flex;
      flex-direction: column;
      gap: 22px;
    }

    .sidebar-brand {
      display: flex;
      flex-direction: column;
      gap: 10px;
      padding-bottom: 18px;
      border-bottom: 1px solid rgba(255, 255, 255, 0.12);
    }

    .sidebar-brand .logo {
      display: inline-flex;
      align-items: center;
      gap: 10px;
      font-size: 22px;
      font-weight: 800;
      letter-spacing: -0.03em;
    }

    .sidebar-brand .meta {
      font-size: 13px;
      line-height: 1.5;
      color: rgba(255, 255, 255, 0.78);
    }

    .profile-chip {
      padding: 14px 16px;
      border-radius: 18px;
      background: rgba(255, 255, 255, 0.1);
      border: 1px solid rgba(255, 255, 255, 0.08);
    }

    .profile-chip .name { font-size: 15px; font-weight: 700; margin-bottom: 2px; }
    .profile-chip .email { font-size: 12px; color: rgba(255, 255, 255, 0.75); word-break: break-word; }

    .profile-badge {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      margin-top: 10px;
      padding: 6px 10px;
      border-radius: 999px;
      background: rgba(255, 255, 255, 0.14);
      font-size: 12px;
      font-weight: 700;
    }

    .nav { display: flex; flex-direction: column; gap: 6px; }

    .nav-item {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 12px;
      width: 100%;
      padding: 13px 14px;
      border-radius: 16px;
      background: transparent;
      color: rgba(255, 255, 255, 0.82);
      border: 1px solid transparent;
      transition: background 0.2s ease, transform 0.2s ease, border-color 0.2s ease;
    }

    .nav-item:hover { background: rgba(255, 255, 255, 0.08); transform: translateX(2px); }
    .nav-item.active { background: rgba(255, 255, 255, 0.14); color: #fff; border-color: rgba(255, 255, 255, 0.14); }
    .nav-item .label { display: flex; align-items: center; gap: 10px; font-size: 14px; font-weight: 600; }
    .nav-item .dot { width: 8px; height: 8px; border-radius: 999px; background: rgba(255, 255, 255, 0.4); }
    .nav-item.active .dot { background: #fff; }

    .sidebar-footer {
      margin-top: auto;
      padding-top: 18px;
      border-top: 1px solid rgba(255, 255, 255, 0.12);
      font-size: 12px;
      color: rgba(255, 255, 255, 0.72);
      display: flex;
      flex-direction: column;
      gap: 10px;
    }

    .sidebar-footer .logout {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      width: fit-content;
      padding: 9px 12px;
      border-radius: 12px;
      background: rgba(255, 255, 255, 0.12);
      color: #fff;
      font-weight: 600;
    }

    .content {
      padding: 28px;
      overflow: auto;
    }

    .topbar {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 18px;
      margin-bottom: 22px;
    }

    .topbar h1 {
      margin: 0;
      font-size: 28px;
      letter-spacing: -0.03em;
    }

    .topbar p {
      margin: 6px 0 0;
      color: var(--muted);
      line-height: 1.5;
    }

    .topbar-badge {
      flex-shrink: 0;
      padding: 10px 14px;
      border-radius: 999px;
      background: linear-gradient(135deg, rgba(240, 192, 64, 0.22), rgba(240, 192, 64, 0.35));
      color: #8a5a00;
      font-size: 12px;
      font-weight: 800;
      letter-spacing: 0.02em;
    }

    .flash {
      padding: 12px 14px;
      border-radius: 16px;
      margin-bottom: 18px;
      font-size: 14px;
      line-height: 1.5;
    }

    .flash.error { background: #fdecec; color: #a02e2e; }
    .flash.success { background: #e9f7ef; color: #155339; }

    .panel {
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: var(--radius-lg);
      box-shadow: var(--shadow);
      padding: 22px;
    }

    .card-grid { display: grid; gap: 16px; }
    .card-grid.cols-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    .card-grid.cols-3 { grid-template-columns: repeat(3, minmax(0, 1fr)); }
    .card-grid.cols-4 { grid-template-columns: repeat(4, minmax(0, 1fr)); }

    .info-card {
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: var(--radius-md);
      box-shadow: var(--shadow);
      padding: 18px;
    }

    .info-card .label { font-size: 12px; color: var(--muted); margin-bottom: 8px; }
    .info-card .value { font-size: 30px; font-weight: 800; letter-spacing: -0.04em; color: var(--brand); }
    .info-card .note { font-size: 12px; color: var(--muted); margin-top: 5px; }

    <?= $this->renderSection('styles') ?>

    @media (max-width: 1100px) {
      .app-shell { grid-template-columns: 1fr; }
      .sidebar { border-bottom-left-radius: 28px; border-bottom-right-radius: 28px; }
    }

    @media (max-width: 800px) {
      .content { padding: 18px; }
      .topbar { flex-direction: column; align-items: flex-start; }
      .card-grid.cols-2,
      .card-grid.cols-3,
      .card-grid.cols-4 { grid-template-columns: 1fr; }
    }
  </style>
</head>
<body>
  <div class="app-shell">
    <aside class="sidebar">
      <div class="sidebar-brand">
        <div class="logo">🥗 NutriPlan</div>
        <div class="meta"><?= esc($pageTitle ?? 'Espace membre') ?></div>
      </div>

      <div class="profile-chip">
        <div class="name"><?= esc($displayName ?? 'Utilisateur') ?></div>
        <div class="email"><?= esc($displayEmail ?? '') ?></div>
        <div class="profile-badge"><?= esc($accountBadge ?? (($isGold ?? false) ? 'Gold' : 'Standard')) ?></div>
      </div>

      <nav class="nav">
        <a class="nav-item <?= ($activeMenu ?? '') === 'dashboard' ? 'active' : '' ?>" href="<?= esc(site_url(session('accountType') === 'admin' ? 'admin/dashboard' : 'dashboard')) ?>">
          <span class="label"><span class="dot"></span> Tableau de bord</span>
        </a>
        <a class="nav-item <?= ($activeMenu ?? '') === 'regimes' ? 'active' : '' ?>" href="<?= esc(site_url('regimes-suggeres')) ?>">
          <span class="label"><span class="dot"></span> Régimes suggérés</span>
        </a>
        <a class="nav-item <?= ($activeMenu ?? '') === 'gold' ? 'active' : '' ?>" href="<?= esc(site_url('option-gold')) ?>">
          <span class="label"><span class="dot"></span> Option Gold</span>
        </a>
      </nav>

      <div class="sidebar-footer">
        <div><?= esc($displayName ?? 'Utilisateur') ?></div>
        <div><?= esc($displayEmail ?? '') ?></div>
        <a class="logout" href="<?= esc(site_url('logout')) ?>">Se deconnecter</a>
      </div>
    </aside>

    <main class="content">
      <div class="topbar">
        <div>
          <h1><?= esc($pageHeading ?? ($pageTitle ?? 'NutriPlan')) ?></h1>
          <p><?= esc($pageSubtitle ?? '') ?></p>
        </div>
        <?php if (! empty($accountBadge)) : ?>
          <div class="topbar-badge"><?= esc($accountBadge) ?></div>
        <?php endif; ?>
      </div>

      <?php if (is_string($flashError = session()->getFlashdata('authError') ?? null)) : ?>
        <div class="flash error"><?= esc($flashError) ?></div>
      <?php endif; ?>

      <?php if (is_string($flashSuccess = session()->getFlashdata('authSuccess') ?? null)) : ?>
        <div class="flash success"><?= esc($flashSuccess) ?></div>
      <?php endif; ?>

      <?= $this->renderSection('content') ?>
    </main>
  </div>
</body>
</html>