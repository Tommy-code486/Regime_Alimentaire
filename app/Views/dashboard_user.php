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
$subscriptionHistory = $subscriptionHistory ?? [];
$historyFilter = (string) ($historyFilter ?? 'all');
?>
<div class="stats-grid">
  <div class="stat-card"><div class="lbl">IMC actuel</div><div class="val"><?= esc($imc ?? '24.2') ?></div><div class="sub">Poids normal</div></div>
  <div class="stat-card"><div class="lbl">Poids actuel</div><div class="val"><?= esc(number_format($poidsActuel, 1, ',', ' ')) ?> kg</div><div class="sub">Objectif : <?= esc(number_format($poidsObjectif, 1, ',', ' ')) ?> kg</div></div>
  <div class="stat-card"><div class="lbl">Régime actif</div><div class="val"><?= esc($regimeActifNom) ?></div><div class="sub"><?= $regimeActifSemaine ? 'Semaine ' . esc((string) $regimeActifSemaine) . '/' . esc((string) $regimeActifDuree) : 'Aucune souscription active' ?></div></div>
  <div class="stat-card"><div class="lbl">Porte-monnaie</div><div class="val"><?= esc(number_format((float) ($solde_portefeuille ?? 15000), 0, ',', ' ')) ?></div><div class="sub">Ar disponibles</div></div>
</div>

<div class="grid2">
  <div class="card imc-card">
    <div class="dashboard-card-head imc-head">
      <div>
        <div class="dashboard-kicker">Santé</div>
        <h3>Votre IMC</h3>
      </div>
      <div class="dashboard-chip imc-chip">Suivi intelligent</div>
    </div>
    <div class="imc-hero">
      <div class="imc-hero-value"><?= esc($imc ?? '24.2') ?></div>
      <div class="imc-hero-label">Indice de Masse Corporelle</div>
      <div class="imc-hero-meter">
        <span style="width:18%"></span>
      </div>
      <div class="imc-hero-scale">
        <span>Maigreur</span>
        <span>Normal</span>
        <span>Surpoids</span>
        <span>Obésité</span>
      </div>
    </div>
    <div class="imc-summary-row">
      <div class="imc-summary-pill">
        <span class="imc-summary-label">Poids</span>
        <strong><?= esc(number_format($poidsActuel, 1, ',', ' ')) ?> kg</strong>
      </div>
      <div class="imc-summary-pill">
        <span class="imc-summary-label">Objectif</span>
        <strong><?= esc(number_format($poidsObjectif, 1, ',', ' ')) ?> kg</strong>
      </div>
    </div>
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
              <div style="font-weight: 600;"><?= esc((string) ($category['nom'] ?? '')) ?></div>
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
          <?= esc((string) session('imc_target_category_name')) ?>
        <?php else: ?>
          <?= esc($selectedObjectifLabel) ?>
        <?php endif; ?>
      </strong>
    </div>
  </div>

</div>

<div class="dashboard-bottom-grid" style="margin-top:16px;">
  <div class="card dashboard-wallet">
    <div class="dashboard-card-head">
      <div>
        <div class="dashboard-kicker">Portefeuille</div>
        <h3>Porte-monnaie</h3>
      </div>
      <div class="dashboard-chip">Disponible</div>
    </div>
    <div class="wallet-box">
      <div><div class="wallet-label">Solde disponible</div><div class="amount"><?= esc(number_format((float) ($solde_portefeuille ?? 15000), 0, ',', ' ')) ?> Ar</div></div>
      <button class="btn-sm" type="button">Recharger</button>
    </div>
    <div class="wallet-note">Entrer un code de recharge</div>
    <form class="code-input" method="post" action="<?= esc(site_url('portefeuille/valider')) ?>">
      <?= csrf_field() ?>
      <input type="text" name="code" placeholder="Code promo (ex: NUTRI2024)" value="<?= esc(old('code')) ?>" required maxlength="50"><button class="btn-sm" type="submit">Valider</button>
    </form>
  </div>

  <div class="card dashboard-section dashboard-history-card">
    <div class="dashboard-section-head premium">
      <div>
        <div class="dashboard-kicker">Suivi</div>
        <h3>Historique des régimes achetés</h3>
        <p>Une vue claire de vos souscriptions précédentes, avec le statut et le montant payé.</p>
      </div>
      <div class="dashboard-history-tools">
        <div class="dashboard-section-count"><?= esc((string) count($subscriptionHistory)) ?> achat(s)</div>
        <div class="history-filters">
          <a class="history-filter <?= $historyFilter === 'all' ? 'active' : '' ?>" href="<?= esc(site_url('dashboard')) ?>">Tout</a>
          <a class="history-filter <?= $historyFilter === 'active' ? 'active' : '' ?>" href="<?= esc(site_url('dashboard?history=active')) ?>">Actifs</a>
          <a class="history-filter <?= $historyFilter === 'ended' ? 'active' : '' ?>" href="<?= esc(site_url('dashboard?history=ended')) ?>">Terminés</a>
        </div>
      </div>
    </div>

    <?php if (! empty($subscriptionHistory)) : ?>
      <div class="history-list premium-list">
        <?php foreach ($subscriptionHistory as $item) : ?>
          <?php
            $dateDebut = ! empty($item['date_debut']) ? date('d/m/Y', strtotime((string) $item['date_debut'])) : 'N/A';
            $dateFin = ! empty($item['date_fin']) ? date('d/m/Y', strtotime((string) $item['date_fin'])) : 'N/A';
            $isActive = ! empty($item['date_fin']) && strtotime((string) $item['date_fin']) >= strtotime(date('Y-m-d'));
            $statusLabel = $isActive ? 'Actif' : 'Terminé';
            $statusClass = $isActive ? 'active' : 'ended';
            $duration = (int) ($item['prix_duree_semaines'] ?? $item['regime_duree'] ?? 0);
            $amount = (float) ($item['montant_paye'] ?? $item['prix_montant'] ?? 0);
          ?>
          <article class="history-item premium-item">
            <div class="history-timeline">
              <span class="history-line"></span>
              <span class="history-dot <?= esc($statusClass) ?>"></span>
            </div>
            <div class="history-main">
              <div class="history-topline">
                <div class="history-name"><?= esc((string) ($item['regime_nom'] ?? 'Régime')) ?></div>
                <span class="history-status <?= esc($statusClass) ?>"><?= esc($statusLabel) ?></span>
              </div>
              <div class="history-desc"><?= esc((string) ($item['regime_description'] ?? 'Souscription enregistrée.')) ?></div>
              <div class="history-meta">
                <span>Du <?= esc($dateDebut) ?> au <?= esc($dateFin) ?></span>
                <span><?= esc((string) ($duration > 0 ? $duration . ' semaines' : 'Durée non précisée')) ?></span>
              </div>
            </div>
            <div class="history-side">
              <div class="history-amount"><?= esc(number_format($amount, 0, ',', ' ')) ?> Ar</div>
              <div class="history-note">Montant payé</div>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    <?php else : ?>
      <div class="history-empty">Aucune souscription enregistrée pour le moment.</div>
    <?php endif; ?>
  </div>
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
