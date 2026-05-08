<?= $this->extend('layouts/app') ?>

<?= $this->section('styles') ?>
.stats-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 14px; margin-bottom: 18px; }
.stat-card {
  background: white;
  border: 1px solid rgba(18, 56, 35, 0.12);
  border-radius: 18px;
  padding: 16px;
  box-shadow: var(--shadow);
}
.stat-card .lbl { font-size: 12px; color: #5f7667; margin-bottom: 6px; }
.stat-card .val { font-size: 28px; font-weight: 800; color: #1a6b45; letter-spacing: -0.03em; }
.stat-card .sub { font-size: 12px; color: #5f7667; margin-top: 3px; }
.grid2 { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px; }
.card {
  background: white;
  border: 1px solid rgba(18, 56, 35, 0.12);
  border-radius: 22px;
  padding: 20px;
  box-shadow: var(--shadow);
}
.card h3 { margin: 0 0 14px; font-size: 16px; letter-spacing: -0.02em; }
.imc-bar {
  height: 12px;
  border-radius: 999px;
  background: linear-gradient(to right, #5bc8f5, #5fc96a, #f0c040, #f07040, #e03030);
  margin: 10px 0;
  position: relative;
}
.imc-cursor { position: absolute; top: -4px; width: 4px; height: 20px; background: #1a6b45; border-radius: 2px; left: 45%; }
.imc-labels { display: flex; justify-content: space-between; gap: 10px; font-size: 10px; color: #5f7667; margin-top: 6px; }
.obj-btn {
  display: flex;
  align-items: center;
  gap: 12px;
  width: 100%;
  padding: 12px 14px;
  border: 1px solid rgba(18, 56, 35, 0.12);
  border-radius: 16px;
  background: #f8fcf9;
  margin-bottom: 8px;
  cursor: pointer;
  font-size: 14px;
  color: #123823;
}
.obj-btn.selected { border-color: #1a6b45; background: #e7f3ec; color: #1a6b45; font-weight: 700; }
.obj-icon { font-size: 22px; }
.wallet-box {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 14px;
  background: #e7f3ec;
  border-radius: 16px;
  padding: 14px 16px;
  margin-bottom: 12px;
}
.wallet-box .amount { font-size: 24px; font-weight: 900; color: #1a6b45; }
.btn-sm {
  padding: 10px 16px;
  background: #1a6b45;
  color: white;
  border: none;
  border-radius: 14px;
  font-size: 13px;
  font-weight: 700;
  cursor: pointer;
}
.code-input { display: flex; gap: 8px; }
.code-input input {
  flex: 1;
  padding: 11px 14px;
  border: 1px solid rgba(18, 56, 35, 0.12);
  border-radius: 14px;
  font-size: 14px;
  background: #f8fcf9;
  color: #123823;
}
.code-input input:focus { outline: none; border-color: #1a6b45; box-shadow: 0 0 0 4px rgba(26, 107, 69, 0.12); }

@media (max-width: 1200px) {
  .stats-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
  .grid2 { grid-template-columns: 1fr; }
}

@media (max-width: 680px) {
  .stats-grid { grid-template-columns: 1fr; }
  .code-input { flex-direction: column; }
}
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="stats-grid">
  <div class="stat-card"><div class="lbl">IMC actuel</div><div class="val"><?= esc($imc ?? '24.2') ?></div><div class="sub">Poids normal</div></div>
  <div class="stat-card"><div class="lbl">Poids actuel</div><div class="val">70 kg</div><div class="sub">Objectif : 65 kg</div></div>
  <div class="stat-card"><div class="lbl">Régime actif</div><div class="val">Keto</div><div class="sub">Semaine 2/8</div></div>
  <div class="stat-card"><div class="lbl">Porte-monnaie</div><div class="val"><?= esc(number_format((float) ($solde_portefeuille ?? 15000), 0, ',', ' ')) ?></div><div class="sub">Ar disponibles</div></div>
</div>

<div class="grid2">
  <div class="card">
    <h3>Votre IMC</h3>
    <div style="font-size:13px;color:#5f7667;margin-bottom:8px;">Indice de Masse Corporelle</div>
    <div style="font-size:36px;font-weight:900;color:#1a6b45;margin-bottom:8px;"><?= esc($imc ?? '24.2') ?></div>
    <div class="imc-bar"><div class="imc-cursor"></div></div>
    <div class="imc-labels"><span>Maigreur<br>&lt; 18.5</span><span>Normal<br>18.5-25</span><span>Surpoids<br>25-30</span><span>Obésité<br>&gt; 30</span></div>
  </div>
  <div class="card">
    <h3>Mon objectif</h3>
    <div class="obj-btn"><span class="obj-icon">⬆️</span><div><div style="font-weight:700;">Augmenter le poids</div><div style="font-size:12px;color:#5f7667;">Prise de masse</div></div></div>
    <div class="obj-btn selected"><span class="obj-icon">⬇️</span><div><div style="font-weight:700;">Réduire le poids</div><div style="font-size:12px;">Perte de poids</div></div></div>
    <div class="obj-btn"><span class="obj-icon">🎯</span><div><div style="font-weight:700;">IMC idéal</div><div style="font-size:12px;color:#5f7667;">Équilibrage</div></div></div>
  </div>
</div>

<div class="card" style="margin-top:16px;">
  <h3>Porte-monnaie</h3>
  <div class="wallet-box">
    <div><div style="font-size:12px;color:#1a6b45;margin-bottom:2px;">Solde disponible</div><div class="amount"><?= esc(number_format((float) ($solde_portefeuille ?? 15000), 0, ',', ' ')) ?> Ar</div></div>
    <button class="btn-sm" type="button">Recharger</button>
  </div>
  <div style="font-size:13px;color:#5f7667;margin-bottom:8px;">Entrer un code de recharge</div>
  <div class="code-input">
    <input type="text" placeholder="Code promo (ex: NUTRI2024)">
    <button class="btn-sm" type="button">Valider</button>
  </div>
</div>
<?= $this->endSection() ?>