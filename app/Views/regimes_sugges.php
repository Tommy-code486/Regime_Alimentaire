
<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<?php
$selectedObjectif = (string) ($selectedObjectif ?? 'equilibre');
$selectedObjectifLabel = (string) ($selectedObjectifLabel ?? 'IMC idéal');
$remiseGold = (float) ($remiseGold ?? 15);
$isGold = (bool) ($isGold ?? false);
?>

<p class="intro">Basé sur votre objectif : <strong><?= esc($selectedObjectifLabel) ?></strong> - IMC actuel : <strong><?= esc((string) ($imc ?? '24.2')) ?></strong></p>

<div class="filter-bar">
  <a class="filter-pill <?= $selectedObjectif === 'reduction' ? 'active' : '' ?>" href="<?= esc(site_url('regimes-suggeres?objectif=reduction')) ?>">⬇️ Perte de poids</a>
  <a class="filter-pill <?= $selectedObjectif === 'augmentation' ? 'active' : '' ?>" href="<?= esc(site_url('regimes-suggeres?objectif=augmentation')) ?>">⬆️ Prise de masse</a>
  <a class="filter-pill <?= $selectedObjectif === 'equilibre' ? 'active' : '' ?>" href="<?= esc(site_url('regimes-suggeres?objectif=equilibre')) ?>">🎯 IMC idéal</a>
</div>

<div class="regimes-grid">
  <?php if (! empty($regimes ?? [])) : ?>
    <?php foreach (($regimes ?? []) as $index => $regime) : ?>
      <?php
      $prices = $regime['prices'] ?? [];
      $recommended = $index === 0;
      $icon = ((float) ($regime['variation_poids'] ?? 0) < 0) ? '🥗' : (((float) ($regime['variation_poids'] ?? 0) > 0) ? '💪' : '⚖️');
      $firstPrice = ! empty($prices) ? (float) ($prices[0]['prix'] ?? 0) : null;
      $firstDuration = ! empty($prices) ? (int) ($prices[0]['duree_semaines'] ?? 0) : (int) ($regime['duree_semaines'] ?? 0);
      $finalPrice = $isGold && $firstPrice !== null ? $firstPrice * (1 - ($remiseGold / 100)) : $firstPrice;
      ?>
      <div class="regime-card <?= $recommended ? 'recommended' : '' ?>">
        <?php if ($recommended) : ?>
          <div class="badge-rec">Recommandé</div>
        <?php endif; ?>
        <div class="badge-gold"><?= esc((string) ($regime['objectif_nom'] ?? 'Objectif')) ?></div>
        <div class="regime-top"><?= esc($icon) ?></div>
        <div class="regime-body">
          <div class="regime-name"><?= esc((string) ($regime['nom'] ?? '')) ?></div>
          <div class="regime-desc"><?= esc((string) ($regime['description'] ?? '')) ?></div>
          <div class="macro-pills">
            <span class="macro-pill pill-meat">Viande <?= esc((string) ($regime['pourcentage_viande'] ?? 0)) ?>%</span>
            <span class="macro-pill pill-fish">Poisson <?= esc((string) ($regime['pourcentage_poisson'] ?? 0)) ?>%</span>
            <span class="macro-pill pill-poultry">Volaille <?= esc((string) ($regime['pourcentage_volaille'] ?? 0)) ?>%</span>
          </div>

          <form method="post" action="<?= esc(site_url('regimes-suggeres/choisir')) ?>">
            <?= csrf_field() ?>
            <input type="hidden" name="regime_id" value="<?= esc((string) ($regime['id'] ?? 0)) ?>">

            <div class="regime-footer" style="align-items: flex-end;">
              <div>
                <div class="duration-tag">📅 <?= esc((string) $firstDuration) ?> semaines</div>
                <?php if ($finalPrice !== null) : ?>
                  <div class="price">
                    <?php if ($isGold) : ?>
                      <span class="price-gold"><?= esc(number_format((float) $firstPrice, 0, ',', ' ')) ?> Ar</span>
                    <?php endif; ?>
                    <?= esc(number_format((float) $finalPrice, 0, ',', ' ')) ?> Ar
                  </div>
                <?php else : ?>
                  <div class="price">Tarif indisponible</div>
                <?php endif; ?>
              </div>
              <div>
                <select name="prix_regime_id" <?= empty($prices) ? 'disabled' : '' ?> style="margin-bottom:8px; border:1px solid var(--border); border-radius:12px; padding:8px; background:white;">
                  <?php foreach ($prices as $price) : ?>
                    <option value="<?= esc((string) ($price['id'] ?? 0)) ?>">
                      <?= esc((string) ($price['duree_semaines'] ?? 0)) ?> sem - <?= esc(number_format((float) ($price['prix'] ?? 0), 0, ',', ' ')) ?> Ar
                    </option>
                  <?php endforeach; ?>
                </select>
                <button class="btn-choose" type="submit" <?= empty($prices) ? 'disabled' : '' ?>>Choisir</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    <?php endforeach; ?>
  <?php else : ?>
    <div class="card" style="grid-column: 1 / -1; text-align:center;">
      <h3>Aucun régime disponible</h3>
      <p class="intro">Aucun régime actif n'est encore disponible pour cet objectif.</p>
    </div>
  <?php endif; ?>
</div>

<div class="export-bar">
  <a class="btn-export" href="<?= esc(site_url('dashboard')) ?>">Retour au dashboard</a>
  <a class="btn-export" href="<?= esc(site_url('option-gold')) ?>">Voir option Gold</a>
</div>
<?= $this->endSection() ?>
