<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<?php
$parametres = $parametres ?? [];
$parametre = $parametre ?? [];
$action = (string) ($action ?? site_url('admin/parametres/store'));
$isEdit = (bool) ($isEdit ?? false);
?>

<div class="card">
  <div class="dashboard-card-head">
    <div>
      <div class="dashboard-kicker">Administration</div>
      <h3><?= esc($isEdit ? 'Modifier un paramètre' : 'Créer un paramètre') ?></h3>
    </div>
    <div class="dashboard-chip">CRUD paramètre</div>
  </div>

  <form method="post" action="<?= esc($action) ?>" style="margin-bottom: 24px;">
    <?= csrf_field() ?>
    <div class="metric-grid">
      <div class="metric-card">
        <label for="id">ID</label>
        <input type="number" id="id" name="id" value="<?= esc(old('id', (string) ($parametre['id'] ?? ''))) ?>" <?= $isEdit ? 'readonly' : 'required' ?> >
      </div>
      <div class="metric-card">
        <label for="cle">Clé</label>
        <input type="text" id="cle" name="cle" value="<?= esc(old('cle', (string) ($parametre['cle'] ?? ''))) ?>" required>
      </div>
      <div class="metric-card">
        <label for="valeur">Valeur</label>
        <input type="text" id="valeur" name="valeur" value="<?= esc(old('valeur', (string) ($parametre['valeur'] ?? ''))) ?>" required>
      </div>
      <div class="metric-card" style="grid-column: 1 / -1;">
        <label for="description">Description</label>
        <textarea id="description" name="description" rows="4"><?= esc(old('description', (string) ($parametre['description'] ?? ''))) ?></textarea>
      </div>
    </div>
    <div class="footer" style="margin-top: 16px;">
      <a class="btn-back" href="<?= esc(site_url('admin/dashboard')) ?>">← Retour admin</a>
      <button class="btn-submit" type="submit"><?= esc($isEdit ? 'Mettre à jour' : 'Créer') ?></button>
    </div>
  </form>
</div>

<div class="card" style="margin-top: 18px;">
  <div class="dashboard-card-head">
    <div>
      <div class="dashboard-kicker">Liste</div>
      <h3>Paramètres existants</h3>
    </div>
    <div class="dashboard-chip"><?= esc((string) count($parametres)) ?> entrées</div>
  </div>

  <div class="history-list premium-list">
    <?php foreach ($parametres as $item) : ?>
      <article class="history-item premium-item">
        <div class="history-main">
          <div class="history-topline">
            <div class="history-name"><?= esc((string) ($item['cle'] ?? '')) ?></div>
            <span class="history-status active">#<?= esc((string) ($item['id'] ?? 0)) ?></span>
          </div>
          <div class="history-desc"><?= esc((string) ($item['description'] ?? '')) ?></div>
          <div class="history-meta"><span>Valeur: <?= esc((string) ($item['valeur'] ?? '')) ?></span><span>Mis à jour: <?= esc((string) ($item['updated_at'] ?? '')) ?></span></div>
        </div>
        <div class="history-side" style="display:flex; gap:8px; align-items:center;">
          <a class="btn-sm" href="<?= esc(site_url('admin/parametres/edit/' . (int) ($item['id'] ?? 0))) ?>">Modifier</a>
          <form method="post" action="<?= esc(site_url('admin/parametres/delete/' . (int) ($item['id'] ?? 0))) ?>">
            <?= csrf_field() ?>
            <button class="btn-sm" type="submit">Supprimer</button>
          </form>
        </div>
      </article>
    <?php endforeach; ?>
  </div>
</div>

<?= $this->endSection() ?>