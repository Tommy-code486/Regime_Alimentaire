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
      <button class="obj-btn <?= $selectedObjectif === 'equilibre' ? 'selected' : '' ?>" type="submit" name="objectif" value="equilibre">
        <span class="obj-icon">🎯</span>
        <div>
          <div class="obj-title">IMC idéal</div>
          <div class="obj-subtitle">Équilibrage</div>
        </div>
      </button>
    </form>
    <div class="wallet-note">Objectif actuel : <strong><?= esc($selectedObjectifLabel) ?></strong></div>
  </div>
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
<?= $this->endSection() ?>
