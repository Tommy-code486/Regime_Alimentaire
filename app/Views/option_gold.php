<?= $this->extend('layouts/app') ?>

<?= $this->section('styles') ?>
.gold-hero { text-align: center; margin-bottom: 28px; }
.gold-hero .icon { font-size: 56px; margin-bottom: 12px; }
.gold-hero h1 { margin: 0 0 8px; font-size: 30px; letter-spacing: -0.03em; }
.gold-hero p { margin: 0 auto; font-size: 15px; color: #5f7667; max-width: 560px; line-height: 1.6; }
.pricing-wrap { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 20px; margin: 0 auto 24px; width: min(100%, 820px); }
.plan-card {
  background: white;
  border-radius: 24px;
  border: 1px solid rgba(18, 56, 35, 0.12);
  padding: 28px;
  text-align: center;
  box-shadow: var(--shadow);
}
.plan-card.gold-card { border: 2px solid #e8a020; background: #fffdf5; }
.plan-name { font-size: 17px; font-weight: 800; color: #123823; margin-bottom: 6px; }
.plan-price { font-size: 36px; font-weight: 900; color: #1a6b45; margin-bottom: 4px; }
.plan-price.gold-price { color: #c07800; }
.plan-per { font-size: 12px; color: #5f7667; margin-bottom: 18px; }
.plan-features { list-style: none; text-align: left; margin: 0 0 20px; padding: 0; }
.plan-features li { display: flex; align-items: center; gap: 8px; padding: 8px 0; font-size: 13px; color: #5f7667; border-bottom: 1px solid rgba(18, 56, 35, 0.08); }
.plan-features li:last-child { border: none; }
.check { color: #1a6b45; font-weight: 900; }
.check-gold { color: #c07800; font-weight: 900; }
.cross { color: #c4c4c4; }
.btn-free,
.btn-gold {
  width: 100%;
  padding: 12px;
  border-radius: 16px;
  font-size: 14px;
  font-weight: 800;
  cursor: pointer;
}
.btn-free { background: transparent; border: 1px solid rgba(18, 56, 35, 0.12); color: #5f7667; }
.btn-gold { background: linear-gradient(135deg, #f0c040, #e8a020); border: none; color: #7a4500; }
.gold-tag { display: inline-block; background: linear-gradient(135deg, #f0c040, #e8a020); color: #7a4500; font-size: 11px; font-weight: 900; padding: 4px 10px; border-radius: 999px; margin-bottom: 12px; }
.wallet-info { font-size: 13px; color: #5f7667; background: white; border-radius: 18px; border: 1px solid rgba(18, 56, 35, 0.12); padding: 14px 18px; text-align: center; max-width: 520px; margin: 0 auto; box-shadow: var(--shadow); }
.wallet-info strong { color: #1a6b45; }

@media (max-width: 900px) {
  .pricing-wrap { grid-template-columns: 1fr; width: min(100%, 560px); }
}
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="gold-hero">
  <div class="icon">⭐</div>
  <h1>Passez à Gold et économisez !</h1>
  <p>Profitez de 15% de remise sur tous les régimes avec un paiement unique. Accès à vie.</p>
</div>

<div class="pricing-wrap">
  <div class="plan-card">
    <div class="plan-name">Gratuit</div>
    <div class="plan-price">0 Ar</div>
    <div class="plan-per">Pour toujours</div>
    <ul class="plan-features">
      <li><span class="check">✓</span> Calcul IMC</li>
      <li><span class="check">✓</span> Suggestions de régimes</li>
      <li><span class="check">✓</span> Porte-monnaie</li>
      <li><span class="cross">✗</span> Remise sur les régimes</li>
      <li><span class="cross">✗</span> Activités sportives premium</li>
      <li><span class="cross">✗</span> Export PDF avancé</li>
    </ul>
    <button class="btn-free" type="button">Plan actuel</button>
  </div>

  <div class="plan-card gold-card">
    <div class="gold-tag">GOLD</div>
    <div class="plan-name">Gold</div>
    <div class="plan-price gold-price">25 000 Ar</div>
    <div class="plan-per">Paiement unique - Accès à vie</div>
    <ul class="plan-features">
      <li><span class="check-gold">✓</span> Calcul IMC</li>
      <li><span class="check-gold">✓</span> Suggestions de régimes</li>
      <li><span class="check-gold">✓</span> Porte-monnaie</li>
      <li><span class="check-gold">✓</span> <strong>15% de remise sur tous les régimes</strong></li>
      <li><span class="check-gold">✓</span> Activités sportives premium</li>
      <li><span class="check-gold">✓</span> Export PDF avancé</li>
    </ul>
    <button class="btn-gold" type="button">Activer Gold - 25 000 Ar</button>
  </div>
</div>

<div class="wallet-info">
  Votre solde actuel : <strong><?= esc(number_format((float) ($solde_portefeuille ?? 15000), 0, ',', ' ')) ?> Ar</strong> - Il vous manque <strong>10 000 Ar</strong> pour activer Gold.
</div>
<?= $this->endSection() ?>