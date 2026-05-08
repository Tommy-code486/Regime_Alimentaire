<?= $this->extend('layouts/app') ?>

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