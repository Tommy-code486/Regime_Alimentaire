<?php $accountType = $accountType ?? (string) session('accountType'); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= esc($pageTitle ?? 'RegimeAlimentaire') ?></title>
  <link rel="stylesheet" href="<?= esc(base_url('assets/css/app.css')) ?>">
</head>
<body>
  <div class="app-shell">
    <aside class="sidebar">
      <div class="sidebar-brand">
        <div class="logo">RegimeAlimentaire</div>
        <div class="meta"><?= esc($pageTitle ?? 'Espace membre') ?></div>
      </div>

      <div class="profile-chip">
        <div class="name"><?= esc($displayName ?? 'Utilisateur') ?></div>
        <div class="email"><?= esc($displayEmail ?? '') ?></div>
        <div class="profile-badge"><?= esc($accountBadge ?? (($isGold ?? false) ? 'Gold' : 'Standard')) ?></div>
      </div>

    <?php if ($accountType === 'admin') { ?>
      <nav class="nav">
        <a class="nav-item <?= ($activeMenu ?? '') === 'stats' ? 'active' : '' ?>" href="<?= esc(site_url('admin/stats')) ?>">
          <span class="label"><span class="dot"></span>Dashboard</span>
        </a>
        <a class="nav-item <?= ($activeMenu ?? '') === 'regimes' ? 'active' : '' ?>" href="<?= esc(site_url('regimes-liste')) ?>">
          <span class="label"><span class="dot"></span> Liste des régimes</span>
        </a>
        <a class="nav-item <?= ($activeMenu ?? '') === 'sports' ? 'active' : '' ?>" href="<?= esc(site_url('sports')) ?>">
          <span class="label"><span class="dot"></span> Activites sportives</span>
        </a>
      </nav>
    <?php }else { ?>
        <nav class="nav">
        <a class="nav-item <?= ($activeMenu ?? '') === 'dashboard' ? 'active' : '' ?>" href="<?= esc(site_url(session('accountType') === 'admin' ? 'admin/dashboard' : 'dashboard')) ?>">
          <span class="label"><span class="dot"></span> Dashboard</span>
        </a>
        <a class="nav-item <?= ($activeMenu ?? '') === 'regimes' ? 'active' : '' ?>" href="<?= esc(site_url('regimes-suggeres')) ?>">
          <span class="label"><span class="dot"></span> Régimes suggérés</span>
        </a>
        <a class="nav-item <?= ($activeMenu ?? '') === 'gold' ? 'active' : '' ?>" href="<?= esc(site_url('option-gold')) ?>">
          <span class="label"><span class="dot"></span> Option Gold</span>
        </a>
      </nav>
    <?php } ?>
      

      <div class="sidebar-footer">
        <div><?= esc($displayName ?? 'Utilisateur') ?></div>
        <div><?= esc($displayEmail ?? '') ?></div>
        <a class="logout" href="<?= esc(site_url('logout')) ?>">Se deconnecter</a>
      </div>
    </aside>

    <main class="content">
      <div class="topbar">
        <div>
          <h1><?= esc($pageHeading ?? ($pageTitle ?? 'RegimeAlimentaire')) ?></h1>
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