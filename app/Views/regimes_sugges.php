
<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<?php
$selectedObjectif = (string) ($selectedObjectif ?? 'equilibre');
$selectedObjectifLabel = (string) ($selectedObjectifLabel ?? 'IMC idéal');
$remiseGold = (float) ($remiseGold ?? 15);
$isGold = (bool) ($isGold ?? false);
$userIMC = (float) ($userIMC ?? 0);
$targetIMC = isset($targetIMC) && $targetIMC !== null ? (float) $targetIMC : null;
$sports = $sports ?? [];
$imcDifference = $targetIMC !== null ? $userIMC - $targetIMC : 0.0;
$absDifference = abs($imcDifference);
$recommendationTitle = 'Votre profil est cohérent';
$recommendationText = 'Continuez avec une alimentation régulière et des activités adaptées à votre objectif.';

if ($targetIMC !== null && $targetIMC > 0) {
  if ($absDifference < 1) {
    $recommendationTitle = 'Vous êtes proche de votre cible';
    $recommendationText = 'Conservez une routine stable avec un régime équilibré et une activité régulière.';
  } elseif ($imcDifference < 0) {
    $recommendationTitle = 'Objectif prise de poids';
    $recommendationText = 'Favorisez les régimes de prise de masse et les activités de renforcement musculaire.';
  } else {
    $recommendationTitle = 'Objectif perte de poids';
    $recommendationText = 'Privilégiez les régimes allégés et les activités cardio pour accompagner la baisse d’IMC.';
  }
}
?>

<div class="regimes-page-shell">
  <section class="card regimes-hero">
    <div class="regimes-hero-top">
      <div>
        <div class="dashboard-kicker">Suggestions personnalisées</div>
        <h2>Régimes recommandés pour votre profil</h2>
        <p>Les propositions ci-dessous sont classées selon votre objectif actuel et les données de votre profil.</p>
      </div>
      <div class="regimes-hero-badge">Basé sur <?= esc($selectedObjectifLabel) ?></div>
    </div>

    <div class="regimes-hero-grid">
      <div class="regime-mini-stat">
        <span>IMC actuel</span>
        <strong><?= esc(number_format($userIMC, 1, ',', ' ')) ?></strong>
      </div>
      <div class="regime-mini-stat">
        <span>IMC cible</span>
        <strong><?= $targetIMC !== null ? esc(number_format($targetIMC, 1, ',', ' ')) : 'N/A' ?></strong>
      </div>
      <div class="regime-mini-stat">
        <span>Écart</span>
        <strong><?= $targetIMC !== null ? esc(number_format($absDifference, 1, ',', ' ')) : 'N/A' ?></strong>
      </div>
      <div class="regime-mini-stat accent">
        <span>Orientation</span>
        <strong><?= esc($recommendationTitle) ?></strong>
      </div>
    </div>

    <div class="regimes-hero-note">
      <?= esc($recommendationText) ?>
    </div>
  </section>

  <div class="regimes-layout">
    <main class="regimes-main">
      <section class="section-card">
        <div class="section-head">
          <div>
            <div class="dashboard-kicker">Choix suggérés</div>
            <h3>Les régimes disponibles</h3>
          </div>
          <div class="section-chip"><?= esc((string) count($regimes ?? [])) ?> proposition(s)</div>
        </div>

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
              <article class="regime-card <?= $recommended ? 'recommended' : '' ?>">
                <div class="regime-card-top">
                  <div class="regime-card-badges">
                    <?php if ($recommended) : ?>
                      <span class="badge-rec">Recommandé</span>
                    <?php endif; ?>
                    <span class="badge-gold"><?= esc((string) ($regime['objectif_nom'] ?? 'Objectif')) ?></span>
                  </div>
                  <div class="regime-card-duration"><?= esc((string) $firstDuration) ?> semaines</div>
                </div>

                <div class="regime-body">
                  <div class="regime-name"><?= esc((string) ($regime['nom'] ?? '')) ?></div>
                  <div class="regime-desc"><?= esc((string) ($regime['description'] ?? '')) ?></div>

                  <div class="regime-metrics">
                    <div><span>Viande</span><strong><?= esc((string) ($regime['pourcentage_viande'] ?? 0)) ?>%</strong></div>
                    <div><span>Poisson</span><strong><?= esc((string) ($regime['pourcentage_poisson'] ?? 0)) ?>%</strong></div>
                    <div><span>Volaille</span><strong><?= esc((string) ($regime['pourcentage_volaille'] ?? 0)) ?>%</strong></div>
                  </div>

                  <form method="post" action="<?= esc(site_url('regimes-suggeres/choisir')) ?>" class="regime-form">
                    <?= csrf_field() ?>
                    <input type="hidden" name="regime_id" value="<?= esc((string) ($regime['id'] ?? 0)) ?>">

                    <div class="regime-footer">
                      <div class="price-block">
                        <div class="duration-tag">Durée sélectionnée</div>
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

                      <div class="choice-block">
                        <select name="prix_regime_id" <?= empty($prices) ? 'disabled' : '' ?> class="price-select">
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
              </article>
            <?php endforeach; ?>
          <?php else : ?>
            <div class="empty-state">
              <h3>Aucun régime disponible</h3>
              <p class="intro">Aucun régime actif n'est encore disponible pour cet objectif.</p>
            </div>
          <?php endif; ?>
        </div>
      </section>
    </main>

    <aside class="regimes-side">
      <section class="section-card insight-card">
        <div class="section-head compact">
          <div>
            <div class="dashboard-kicker">Analyse</div>
            <h3>Comparaison IMC</h3>
          </div>
          <div class="section-chip">Repère rapide</div>
        </div>

        <div class="comparison-grid">
          <div class="comparison-stat">
            <span>IMC actuel</span>
            <strong><?= esc(number_format($userIMC, 1, ',', ' ')) ?></strong>
          </div>
          <div class="comparison-stat">
            <span>IMC cible</span>
            <strong><?= $targetIMC !== null ? esc(number_format($targetIMC, 1, ',', ' ')) : 'N/A' ?></strong>
          </div>
        </div>

        <div class="comparison-note">
          <?= esc($recommendationText) ?>
        </div>
      </section>

      <?php if (! empty($sports)): ?>
        <section class="section-card sport-card-panel">
          <div class="section-head compact">
            <div>
              <div class="dashboard-kicker">Complément</div>
              <h3>Activités recommandées</h3>
            </div>
            <div class="section-chip">Sport</div>
          </div>

          <div class="sports-list">
            <?php foreach ($sports as $sport): ?>
              <article class="sport-item">
                <div class="sport-icon">🏃</div>
                <div class="sport-content">
                  <div class="sport-name"><?= esc((string) ($sport['nom'] ?? '')) ?></div>
                  <?php if (! empty($sport['description'])): ?>
                    <div class="sport-desc"><?= esc((string) ($sport['description'] ?? '')) ?></div>
                  <?php endif; ?>
                  <div class="sport-meta">Calories/h: <?= esc((string) ($sport['calories_par_heure'] ?? 0)) ?></div>
                </div>
              </article>
            <?php endforeach; ?>
          </div>

          <div class="comparison-note">
            <strong>Conseil :</strong> pratiquez 2 à 3 de ces activités régulièrement pour renforcer l'effet de votre régime.
          </div>
        </section>
      <?php endif; ?>
    </aside>
  </div>

  <div class="export-bar">
    <a class="btn-export" href="<?= esc(site_url('dashboard')) ?>">Retour au dashboard</a>
    <a class="btn-export" href="<?= esc(site_url('option-gold')) ?>">Voir option Gold</a>
    <a class="btn-export" href="<?= esc(site_url('exportPDF')) ?>">Exporter en PDF</a>
  </div>
</div>
<?= $this->endSection() ?>
