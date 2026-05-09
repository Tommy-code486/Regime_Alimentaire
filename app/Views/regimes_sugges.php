
<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<?php
$selectedObjectif = (string) ($selectedObjectif ?? 'equilibre');
$selectedObjectifLabel = (string) ($selectedObjectifLabel ?? 'IMC idéal');
$remiseGold = (float) ($remiseGold ?? 15);
$isGold = (bool) ($isGold ?? false);
$userIMC = (float) ($userIMC ?? 0);
$targetIMC = ($targetIMC !== null) ? (float) $targetIMC : null;
$sports = $sports ?? [];
?>

<?php if ($targetIMC !== null && $targetIMC > 0): ?>
  <div class="card" style="margin-bottom: 24px;">
    <h3>Comparaison IMC</h3>
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-top: 12px;">
      <div>
        <div style="font-size: 13px; color: #666; margin-bottom: 6px;">IMC actuel</div>
        <div style="font-size: 28px; font-weight: 700;"><?= esc(number_format($userIMC, 1, ',', ' ')) ?></div>
      </div>
      <div>
        <div style="font-size: 13px; color: #666; margin-bottom: 6px;">IMC cible</div>
        <div style="font-size: 28px; font-weight: 700;"><?= esc(number_format($targetIMC, 1, ',', ' ')) ?></div>
      </div>
    </div>

    <div style="margin-top: 12px; padding: 12px; background: #fafafa; border-radius: 6px;">
      <?php 
      $imcDifference = $userIMC - $targetIMC;
      $absDifference = abs($imcDifference);
      
      if ($absDifference < 1):
      ?>
        <strong>Très proche de l'IMC cible.</strong> Maintenez votre poids avec un régime équilibré et une activité régulière.
      <?php elseif ($imcDifference < 0): ?>
        <strong>Prise de poids recommandée.</strong> Environ <strong><?= esc(number_format($absDifference * 5, 1, ',', ' ')) ?> kg</strong> à gagner. Privilégiez les régimes de prise de masse et la musculation.
      <?php else: ?>
        <strong>Perte de poids recommandée.</strong> Environ <strong><?= esc(number_format($absDifference * 5, 1, ',', ' ')) ?> kg</strong> à perdre. Privilégiez les régimes de perte de poids et le cardio.
      <?php endif; ?>
    </div>
  </div>
<?php endif; ?>

<p class="intro">Basé sur votre objectif : <strong><?= esc($selectedObjectifLabel) ?></strong> - IMC actuel : <strong><?= esc((string) ($imc ?? '24.2')) ?></strong></p>

<!-- filtres supprimés : affichage uniquement des régimes suggérés -->

<div class="regimes-grid">
  <?php if (! empty($regimes ?? [])) : ?>
    <?php foreach (($regimes ?? []) as $index => $regime) : ?>
      <?php
      $prices = $regime['prices'] ?? [];
      $recommended = $index === 0;
      $firstPrice = ! empty($prices) ? (float) ($prices[0]['prix'] ?? 0) : null;
      $firstDuration = ! empty($prices) ? (int) ($prices[0]['duree_semaines'] ?? 0) : (int) ($regime['duree_semaines'] ?? 0);
      $finalPrice = $isGold && $firstPrice !== null ? $firstPrice * (1 - ($remiseGold / 100)) : $firstPrice;
      ?>
      <div class="regime-card <?= $recommended ? 'recommended' : '' ?>">
        <?php if ($recommended) : ?>
          <div class="badge-rec">Recommandé</div>
        <?php endif; ?>
        <div class="badge-gold"><?= esc((string) ($regime['objectif_nom'] ?? 'Objectif')) ?></div>
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
                <div class="duration-tag"><?= esc((string) $firstDuration) ?> semaines</div>
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
                <select name="prix_regime_id" <?= empty($prices) ? 'disabled' : '' ?> style="margin-bottom:8px; border:1px solid var(--border); border-radius:6px; padding:8px; background:white;">
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

<?php if (! empty($sports)): ?>
<!-- Section Activités Sportives Recommandées -->
<div class="card" style="margin-top: 24px;">
  <h3>Activités sportives recommandées</h3>
  <p style="color: #666; margin-bottom: 12px;">Complétez votre régime avec ces activités :</p>

  <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 12px;">
    <?php foreach ($sports as $sport): ?>
      <div style="padding: 12px; background: #fafafa; border-radius: 8px; border:1px solid var(--border);">
        <div style="font-weight: 600; margin-bottom: 6px;"><?= esc($sport['nom'] ?? '') ?></div>
        <?php if (! empty($sport['description'])): ?>
          <div style="font-size: 13px; color: #666; margin-bottom: 8px;"><?= esc($sport['description']) ?></div>
        <?php endif; ?>
        <div style="font-size: 12px; color: #999;">Calories/h: <?= esc((string) ($sport['calories_par_heure'] ?? 0)) ?></div>
      </div>
    <?php endforeach; ?>
  </div>

  <div style="margin-top: 12px; padding: 10px; background: #f7f7f7; border-radius: 6px;">
    <strong>Conseil :</strong> Pratiquez 2-3 de ces activités régulièrement.
  </div>
</div>
<?php endif; ?>

<div class="export-bar">
  <a class="btn-export" href="<?= esc(site_url('dashboard')) ?>">Retour au dashboard</a>
  <a class="btn-export" href="<?= esc(site_url('option-gold')) ?>">Voir option Gold</a>
</div>
<?= $this->endSection() ?>
