<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<?php
$wallet = (float) ($solde_portefeuille ?? 0);
$goldPrice = (float) ($goldPrice ?? 250000);
$goldMissing = (float) ($goldMissing ?? max(0, $goldPrice - $wallet));
$isGold = (bool) ($isGold ?? false);
?>
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
    <div class="plan-price gold-price"><?= esc(number_format($goldPrice, 0, ',', ' ')) ?> Ar</div>
    <div class="plan-per">Paiement unique - Accès à vie</div>
    <ul class="plan-features">
      <li><span class="check-gold">✓</span> Calcul IMC</li>
      <li><span class="check-gold">✓</span> Suggestions de régimes</li>
      <li><span class="check-gold">✓</span> Porte-monnaie</li>
      <li><span class="check-gold">✓</span> <strong>15% de remise sur tous les régimes</strong></li>
      <li><span class="check-gold">✓</span> Activités sportives premium</li>
      <li><span class="check-gold">✓</span> Export PDF avancé</li>
    </ul>
    <?php if ($isGold) : ?>
      <button class="btn-free" type="button" disabled>Option Gold déjà active</button>
    <?php else : ?>
      <form method="post" action="<?= esc(site_url('option-gold/activer')) ?>">
        <?= csrf_field() ?>
        <button class="btn-gold" type="submit">Activer Gold - <?= esc(number_format($goldPrice, 0, ',', ' ')) ?> Ar</button>
      </form>
    <?php endif; ?>
  </div>
</div>

<div class="wallet-info">
  Votre solde actuel : <strong><?= esc(number_format($wallet, 0, ',', ' ')) ?> Ar</strong>
  <?php if ($isGold) : ?>
    - Votre compte bénéficie déjà de la remise Gold.
  <?php elseif ($goldMissing > 0) : ?>
    - Il vous manque <strong><?= esc(number_format($goldMissing, 0, ',', ' ')) ?> Ar</strong> pour activer Gold.
  <?php else : ?>
    - Solde suffisant pour activer Gold dès maintenant.
  <?php endif; ?>
</div>
<?= $this->endSection() ?>