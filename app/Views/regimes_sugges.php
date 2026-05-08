
<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<p class="intro">Basé sur votre objectif : <strong>Réduire le poids</strong> - IMC actuel : <strong><?= esc($imc ?? '24.2') ?></strong></p>

<div class="filter-bar">
  <div class="filter-pill active">Tous</div>
  <div class="filter-pill">⬇️ Perte de poids</div>
  <div class="filter-pill">⬆️ Prise de masse</div>
  <div class="filter-pill">🎯 IMC idéal</div>
</div>

<div class="regimes-grid">
  <div class="regime-card recommended">
    <div class="badge-rec">Recommandé</div>
    <div class="regime-top">🥗</div>
    <div class="regime-body">
      <div class="regime-name">Régime Méditerranéen</div>
      <div class="regime-desc">Riche en légumes, poissons et huile d'olive. Idéal pour perdre 3-5 kg.</div>
      <div class="macro-pills">
        <span class="macro-pill pill-meat">Viande 20%</span>
        <span class="macro-pill pill-fish">Poisson 40%</span>
        <span class="macro-pill pill-poultry">Volaille 40%</span>
      </div>
      <div class="regime-footer">
        <div>
          <div class="duration-tag">📅 8 semaines</div>
          <div class="price"><span class="price-gold">5 900 Ar</span> 5 015 Ar</div>
        </div>
        <button class="btn-choose" type="button">Choisir</button>
      </div>
    </div>
  </div>

  <div class="regime-card">
    <div class="regime-top">🥩</div>
    <div class="regime-body">
      <div class="regime-name">Régime Keto</div>
      <div class="regime-desc">Très faible en glucides, fort en lipides. Perte rapide jusqu'à 8 kg.</div>
      <div class="macro-pills">
        <span class="macro-pill pill-meat">Viande 50%</span>
        <span class="macro-pill pill-fish">Poisson 20%</span>
        <span class="macro-pill pill-poultry">Volaille 30%</span>
      </div>
      <div class="regime-footer">
        <div>
          <div class="duration-tag">📅 6 semaines</div>
          <div class="price">7 500 Ar</div>
        </div>
        <button class="btn-choose" type="button">Choisir</button>
      </div>
    </div>
  </div>

  <div class="regime-card">
    <div class="badge-gold">Gold</div>
    <div class="regime-top">🌿</div>
    <div class="regime-body">
      <div class="regime-name">Régime Végétarien</div>
      <div class="regime-desc">Sans viande ni poisson. Équilibré et sain pour atteindre l'IMC idéal.</div>
      <div class="macro-pills">
        <span class="macro-pill pill-meat">Viande 0%</span>
        <span class="macro-pill pill-fish">Poisson 0%</span>
        <span class="macro-pill pill-poultry">Volaille 0%</span>
      </div>
      <div class="regime-footer">
        <div>
          <div class="duration-tag">📅 10 semaines</div>
          <div class="price"><span class="price-gold">6 000 Ar</span> 5 100 Ar</div>
        </div>
        <button class="btn-choose" type="button">Choisir</button>
      </div>
    </div>
  </div>
</div>

<div class="export-bar">
  <button class="btn-export" type="button">Exporter en PDF</button>
  <button class="btn-export" type="button">Imprimer</button>
</div>
<?= $this->endSection() ?>
