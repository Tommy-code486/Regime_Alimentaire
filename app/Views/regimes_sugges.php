
<?= $this->extend('layouts/app') ?>

<?= $this->section('styles') ?>
.intro { font-size: 14px; color: #5f7667; margin-bottom: 18px; }
.filter-bar { display: flex; gap: 8px; margin-bottom: 18px; flex-wrap: wrap; }
.filter-pill {
  padding: 8px 14px;
  border-radius: 999px;
  border: 1px solid rgba(18, 56, 35, 0.12);
  background: white;
  font-size: 13px;
  cursor: pointer;
  color: #5f7667;
}
.filter-pill.active { background: #1a6b45; color: white; border-color: #1a6b45; font-weight: 700; }
.regimes-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 16px; }
.regime-card {
  background: white;
  border-radius: 22px;
  border: 1px solid rgba(18, 56, 35, 0.12);
  overflow: hidden;
  position: relative;
  box-shadow: var(--shadow);
}
.regime-card.recommended { border: 2px solid #1a6b45; }
.badge-rec {
  position: absolute;
  top: 10px;
  right: 10px;
  background: #1a6b45;
  color: white;
  font-size: 11px;
  font-weight: 800;
  padding: 4px 10px;
  border-radius: 999px;
}
.badge-gold {
  position: absolute;
  top: 10px;
  left: 10px;
  background: linear-gradient(135deg, #f0c040, #e8a020);
  color: #7a4500;
  font-size: 11px;
  font-weight: 900;
  padding: 4px 10px;
  border-radius: 999px;
}
.regime-top { height: 92px; display: flex; align-items: center; justify-content: center; font-size: 48px; background: #e7f3ec; }
.regime-body { padding: 16px; }
.regime-name { font-size: 17px; font-weight: 800; color: #123823; margin-bottom: 4px; }
.regime-desc { font-size: 12px; color: #5f7667; margin-bottom: 12px; line-height: 1.6; }
.macro-pills { display: flex; gap: 6px; flex-wrap: wrap; margin-bottom: 12px; }
.macro-pill { padding: 4px 8px; border-radius: 999px; font-size: 11px; font-weight: 700; }
.pill-meat { background: #ffe0cc; color: #a03000; }
.pill-fish { background: #cce0ff; color: #0040a0; }
.pill-poultry { background: #fff0cc; color: #806000; }
.regime-footer { display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-top: 8px; }
.price { font-size: 15px; font-weight: 900; color: #1a6b45; }
.price-gold { text-decoration: line-through; color: #5f7667; font-size: 12px; margin-right: 4px; }
.duration-tag { font-size: 11px; color: #5f7667; }
.btn-choose {
  padding: 9px 16px;
  background: #1a6b45;
  color: white;
  border: none;
  border-radius: 14px;
  font-size: 13px;
  font-weight: 700;
  cursor: pointer;
}
.btn-choose:hover { background: #155a39; }
.export-bar { display: flex; gap: 10px; margin-top: 22px; flex-wrap: wrap; }
.btn-export {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 10px 16px;
  border: 1px solid rgba(18, 56, 35, 0.12);
  border-radius: 14px;
  background: white;
  font-size: 13px;
  cursor: pointer;
  color: #123823;
}
.btn-export:hover { background: #f7fbf8; }

@media (max-width: 1200px) {
  .regimes-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
}

@media (max-width: 760px) {
  .regimes-grid { grid-template-columns: 1fr; }
}
<?= $this->endSection() ?>

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
