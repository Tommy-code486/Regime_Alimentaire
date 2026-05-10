<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<?php
$selectedObjectif = (string) ($selectedObjectif ?? 'equilibre');
$selectedObjectifLabel = (string) ($selectedObjectifLabel ?? 'IMC idéal');
$poidsActuel = (float) ($poidsActuel ?? 0);
$poidsObjectif = (float) ($poidsObjectif ?? 0);
$regimeActifNom = (string) ($regimeActifNom ?? 'Aucun');
$regimeActifSemaine = $regimeActifSemaine ?? null;
$regimeActifDuree = (int) ($regimeActifDuree ?? 0);
$regimeActifDescription = trim((string) ($regimeActifDescription ?? ''));
$regimeActifVariation = (float) ($regimeActifVariation ?? 0);
$regimeActifViande = (int) ($regimeActifViande ?? 0);
$regimeActifPoisson = (int) ($regimeActifPoisson ?? 0);
$regimeActifVolaille = (int) ($regimeActifVolaille ?? 0);
$hasRegimeActif = $regimeActifNom !== 'Aucun';
?>
<div class="stats-grid">
  <div class="stat-card"><div class="lbl">IMC actuel</div><div class="val"><?= esc($imc ?? '24.2') ?></div><div class="sub">Poids normal</div></div>
  <div class="stat-card"><div class="lbl">Poids actuel</div><div class="val"><?= esc(number_format($poidsActuel, 1, ',', ' ')) ?> kg</div><div class="sub">Objectif : <?= esc(number_format($poidsObjectif, 1, ',', ' ')) ?> kg</div></div>
  <div class="stat-card"><div class="lbl">Régime actif</div><div class="val"><?= esc($regimeActifNom) ?></div><div class="sub"><?= $regimeActifSemaine ? 'Semaine ' . esc((string) $regimeActifSemaine) . '/' . esc((string) $regimeActifDuree) : 'Aucune souscription active' ?></div></div>
  <div class="stat-card"><div class="lbl">Porte-monnaie</div><div class="val"><?= esc(number_format((float) ($solde_portefeuille ?? 15000), 0, ',', ' ')) ?></div><div class="sub">Ar disponibles</div></div>
</div>

<div class="grid2">
  <div class="card">
    <h3>Votre IMC</h3>
    <div class="imc-caption">Indice de Masse Corporelle</div>
    <div class="imc-value-big"><?= esc($imc ?? '24.2') ?></div>
    <div class="imc-bar"><div class="imc-cursor"></div></div>
    <div class="imc-labels"><span>Maigreur<br>&lt; 18.5</span><span>Normal<br>18.5-25</span><span>Surpoids<br>25-30</span><span>Obésité<br>&gt; 30</span></div>
  </div>
  <div class="card">
    <h3>Mon objectif</h3>
    <form method="post" action="<?= esc(site_url('dashboard/objectif')) ?>">
      <?= csrf_field() ?>
      <button class="obj-btn <?= $selectedObjectif === 'augmentation' ? 'selected' : '' ?>" type="submit" name="objectif" value="augmentation">
        <span class="obj-icon">⬆️</span>
        <div>
          <div class="obj-title">Augmenter le poids</div>
          <div class="obj-subtitle">Prise de masse</div>
        </div>
      </button>
      <button class="obj-btn <?= $selectedObjectif === 'reduction' ? 'selected' : '' ?>" type="submit" name="objectif" value="reduction">
        <span class="obj-icon">⬇️</span>
        <div>
          <div class="obj-title">Réduire le poids</div>
          <div class="obj-subtitle">Perte de poids</div>
        </div>
      </button>
      <button class="obj-btn" type="button" onclick="toggleIMCCategories()" style="position:relative;">
        <span class="obj-icon">🎯</span>
        <div>
          <div class="obj-title">IMC idéal</div>
          <div class="obj-subtitle">Équilibrage</div>
        </div>
      </button>
    </form>
    
    <!-- Catégories IMC - Affichage dynamique -->
    <div id="imc-categories-container" style="display: none; margin-top: 16px; padding: 16px; background: #f8f9fa; border-radius: 12px;">
      <div style="font-weight: 600; margin-bottom: 12px;">Sélectionnez votre catégorie IMC cible :</div>
      <form method="post" action="<?= esc(site_url('dashboard/imc-target')) ?>" id="imc-form">
        <?= csrf_field() ?>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px;">
          <?php foreach (($imcCategories ?? []) as $category): ?>
            <button class="imc-category-btn" type="submit" name="imc_category_id" value="<?= esc((string) ($category['id'] ?? 0)) ?>" style="padding: 12px; border: 1px solid #ddd; border-radius: 8px; background: white; cursor: pointer; text-align: left; transition: all 0.3s;">
              <div style="font-weight: 600;"><?= esc($category['nom'] ?? '') ?></div>
              <div style="font-size: 12px; color: #666; margin-top: 4px;">
                IMC: <?= esc(number_format((float) ($category['imc_min'] ?? 0), 1, ',', ' ')) ?> - <?= esc(number_format((float) ($category['imc_max'] ?? 0), 1, ',', ' ')) ?>
              </div>
            </button>
          <?php endforeach; ?>
        </div>
      </form>
    </div>

    <div class="wallet-note">
      Objectif actuel : <strong>
        <?php if (session('imc_target_category_name')): ?>
          <?= esc(session('imc_target_category_name')) ?>
        <?php else: ?>
          <?= esc($selectedObjectifLabel) ?>
        <?php endif; ?>
      </strong>
    </div>
  </div>

</div>

<div class="card" style="margin-top:16px;">
  <h3>Détails du régime actif</h3>
  <?php if (! $hasRegimeActif): ?>
    <div class="wallet-note">Aucune souscription active pour afficher les détails.</div>
  <?php else: ?>
    <div style="display:grid; gap:10px;">
      <div><strong>Nom :</strong> <?= esc($regimeActifNom) ?></div>
      <div><strong>Description :</strong> <?= $regimeActifDescription !== '' ? esc($regimeActifDescription) : 'Non renseignée' ?></div>
      <div><strong>Durée :</strong> <?= esc((string) $regimeActifDuree) ?> semaine<?= $regimeActifDuree > 1 ? 's' : '' ?></div>
      <div><strong>Composition :</strong>
        <?php if (($regimeActifViande + $regimeActifPoisson + $regimeActifVolaille) > 0): ?>
          <?= esc((string) $regimeActifViande) ?>% viande, <?= esc((string) $regimeActifPoisson) ?>% poisson, <?= esc((string) $regimeActifVolaille) ?>% volaille
        <?php else: ?>
          Non renseignée
        <?php endif; ?>
      </div>
      <div><strong>Variation de poids estimée :</strong> <?= esc(number_format($regimeActifVariation, 1, ',', ' ')) ?> kg</div>
    </div>
  <?php endif; ?>
</div>

<div class="card" style="margin-top:16px;">
  <h3>Porte-monnaie</h3>
  <div class="wallet-box">
    <div><div class="wallet-label">Solde disponible</div><div class="amount"><?= esc(number_format((float) ($solde_portefeuille ?? 15000), 0, ',', ' ')) ?> Ar</div></div>
    <button class="btn-sm" type="button">Recharger</button>
  </div>
  <div class="wallet-note">Entrer un code de recharge</div>
  <form class="code-input" method="post" action="<?= esc(site_url('portefeuille/valider')) ?>">
    <?= csrf_field() ?>
    <input type="text" name="code" placeholder="Code promo (ex: NUTRI2024)" value="<?= esc(old('code')) ?>" required maxlength="50">    <button class="btn-sm" type="submit">Valider</button>
  </form>
</div>

<script>
function toggleIMCCategories() {
  const container = document.getElementById('imc-categories-container');
  if (container.style.display === 'none' || container.style.display === '') {
    container.style.display = 'block';
  } else {
    container.style.display = 'none';
  }
}

// Ajouter des événements de survol aux boutons de catégories IMC
document.querySelectorAll('.imc-category-btn').forEach(btn => {
  btn.addEventListener('mouseenter', function() {
    this.style.backgroundColor = '#e8f0fe';
    this.style.borderColor = '#2196F3';
  });
  btn.addEventListener('mouseleave', function() {
    this.style.backgroundColor = 'white';
    this.style.borderColor = '#ddd';
  });
});
</script>
<?= $this->endSection() ?>
