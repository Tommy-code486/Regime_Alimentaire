<?= $this->extend('layouts/app') ?>

<?php
$regime = $regime ?? [];
$mode = $mode ?? 'create';
$isEdit = $mode === 'edit';
$prices = $regime['prices'] ?? [];
$priceRows = [];

for ($i = 1; $i <= 3; $i++) {
    $priceRows[] = [
        'duration' => $prices[$i - 1]['duree_semaines'] ?? old('prix_duree_' . $i) ?? '',
        'price' => $prices[$i - 1]['prix'] ?? old('prix_montant_' . $i) ?? '',
    ];
}
?>

<?= $this->section('content') ?>
<div class="regime-form-layout">
  <div class="form-panel">
    <div class="form-header">
      <div>
        <div class="card-kicker"><?= $isEdit ? 'Modifier' : 'Création' ?> de régime</div>
        <h2 class="form-title"><?= $isEdit ? 'Mettre à jour le régime' : 'Créer un nouveau régime' ?></h2>
        <p class="form-subtitle">Définissez les proportions, la variation de poids et les tarifs selon la durée.</p>
      </div>
      <a class="btn-export" href="<?= esc(site_url('regimes-liste')) ?>">← Retour</a>
    </div>

    <form action="<?= esc(site_url($isEdit ? 'regimes/' . $regime['id'] . '/update' : 'regimes')) ?>" method="post" class="regime-form">
      <?= csrf_field() ?>
      <div class="field">
        <label for="nom">Nom du régime</label>
        <input id="nom" name="nom" type="text" value="<?= esc(old('nom', $regime['nom'] ?? '')) ?>" required>
      </div>

      <div class="field">
        <label for="description">Description</label>
        <textarea id="description" name="description" rows="4" required><?= esc(old('description', $regime['description'] ?? '')) ?></textarea>
      </div>

      <div class="card-grid cols-3">
        <div class="field">
          <label for="pourcentage_viande">% Viande</label>
          <input id="pourcentage_viande" name="pourcentage_viande" type="number" min="0" max="100" value="<?= esc(old('pourcentage_viande', $regime['pourcentage_viande'] ?? 0)) ?>" required>
        </div>
        <div class="field">
          <label for="pourcentage_poisson">% Poisson</label>
          <input id="pourcentage_poisson" name="pourcentage_poisson" type="number" min="0" max="100" value="<?= esc(old('pourcentage_poisson', $regime['pourcentage_poisson'] ?? 0)) ?>" required>
        </div>
        <div class="field">
          <label for="pourcentage_volaille">% Volaille</label>
          <input id="pourcentage_volaille" name="pourcentage_volaille" type="number" min="0" max="100" value="<?= esc(old('pourcentage_volaille', $regime['pourcentage_volaille'] ?? 0)) ?>" required>
        </div>
      </div>

      <div class="card-grid cols-2">
        <div class="field">
          <label for="variation_poids">Variation de poids (kg)</label>
          <input id="variation_poids" name="variation_poids" type="number" step="0.1" value="<?= esc(old('variation_poids', $regime['variation_poids'] ?? 0)) ?>" required>
        </div>
        <div class="field">
          <label for="duree_semaines">Durée du régime (semaines)</label>
          <input id="duree_semaines" name="duree_semaines" type="number" min="1" value="<?= esc(old('duree_semaines', $regime['duree_semaines'] ?? 4)) ?>" required>
        </div>
      </div>

      <div class="field">
        <label for="id_objectif">Objectif</label>
        <select id="id_objectif" name="id_objectif" required>
          <option value="">Choisir un objectif</option>
          <?php foreach (($objectives ?? []) as $objective) : ?>
            <option value="<?= esc((string) $objective['id']) ?>" <?= (string) old('id_objectif', $regime['id_objectif'] ?? '') === (string) $objective['id'] ? 'selected' : '' ?>>
              <?= esc((string) $objective['nom']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="field">
        <label>Tarifs par durée</label>
        <div class="tarif-grid">
          <?php foreach ($priceRows as $index => $row) : ?>
            <div class="tarif-row">
              <div class="field">
                <label for="prix_duree_<?= $index + 1 ?>">Durée <?= $index + 1 ?></label>
                <input id="prix_duree_<?= $index + 1 ?>" name="prix_duree_<?= $index + 1 ?>" type="number" min="1" value="<?= esc((string) $row['duration']) ?>" placeholder="Semaines">
              </div>
              <div class="field">
                <label for="prix_montant_<?= $index + 1 ?>">Prix <?= $index + 1 ?></label>
                <input id="prix_montant_<?= $index + 1 ?>" name="prix_montant_<?= $index + 1 ?>" type="number" min="0" step="0.01" value="<?= esc((string) $row['price']) ?>" placeholder="Ar">
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>

      <label class="toggle-row">
        <input type="checkbox" name="actif" value="1" <?= (int) ($regime['actif'] ?? 1) === 1 ? 'checked' : '' ?>>
        <span>Régime actif</span>
      </label>

      <div class="footer">
        <a class="btn-back" href="<?= esc(site_url('regimes-liste')) ?>">Annuler</a>
        <button class="btn-submit" type="submit"><?= $isEdit ? 'Mettre à jour' : 'Créer le régime' ?></button>
      </div>
    </form>
  </div>

  <aside class="preview-panel">
    <div class="card">
      <h3>Aperçu</h3>
      <div class="regime-preview-card recommended">
        <div class="badge-rec">Régime</div>
        <div class="regime-top">🥗</div>
        <div class="regime-body">
          <div class="regime-name"><?= esc($regime['nom'] ?? 'Nom du régime') ?></div>
          <div class="regime-desc"><?= esc($regime['description'] ?? 'Description du régime') ?></div>
          <div class="macro-pills">
            <span class="macro-pill pill-meat"><?= esc(old('pourcentage_viande', $regime['pourcentage_viande'] ?? 0)) ?>% viande</span>
            <span class="macro-pill pill-fish"><?= esc(old('pourcentage_poisson', $regime['pourcentage_poisson'] ?? 0)) ?>% poisson</span>
            <span class="macro-pill pill-poultry"><?= esc(old('pourcentage_volaille', $regime['pourcentage_volaille'] ?? 0)) ?>% volaille</span>
          </div>
          <div class="regime-meta">
            <span class="duration-tag">Durée : <?= esc(old('duree_semaines', $regime['duree_semaines'] ?? 0)) ?> semaines</span>
            <span class="duration-tag">Variation : <?= esc(old('variation_poids', $regime['variation_poids'] ?? 0)) ?> kg</span>
          </div>
        </div>
      </div>
    </div>
  </aside>
</div>
<?= $this->endSection() ?>