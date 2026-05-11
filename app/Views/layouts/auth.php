<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= esc($pageTitle ?? 'NutriPlan') ?></title>
  <link rel="stylesheet" href="<?= esc(base_url('assets/css/auth.css')) ?>">
</head>
<body>
  <div class="auth-shell">
    <section class="auth-hero">
      <div class="hero-card">
        <div class="brand"><span class="brand-badge">🥗</span>RegimeAlimentaire</div>
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