<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<?php
$userProfile = $userProfile ?? [];
$profileCompletion = (int) ($profileCompletion ?? 0);
$missingFields = $missingFields ?? [];
$profileCompletionClass = (string) ($profileCompletionClass ?? 'warning');
$formAction = (string) ($formAction ?? site_url('profiles/update'));
?>

<div class="stats-grid">
  <div class="stat-card">
    <div class="lbl">Complétion</div>
    <div class="val"><?= esc((string) $profileCompletion) ?>%</div>
    <div class="sub">Profil <?= esc($profileCompletionClass) ?></div>
  </div>
  <div class="stat-card">
    <div class="lbl">IMC</div>
    <div class="val"><?= esc(number_format((float) ($userProfile['imc'] ?? 0), 1, ',', ' ')) ?></div>
    <div class="sub">Indice calculé automatiquement</div>
  </div>
  <div class="stat-card">
    <div class="lbl">Porte-monnaie</div>
    <div class="val"><?= esc(number_format((float) ($userProfile['solde_portefeuille'] ?? 0), 0, ',', ' ')) ?> Ar</div>
    <div class="sub">Solde enregistré</div>
  </div>
</div>

<div class="grid2">
  <div class="card">
    <div class="dashboard-card-head">
      <div>
        <div class="dashboard-kicker">Profil utilisateur</div>
        <h3>Compléter mes informations</h3>
      </div>
      <div class="dashboard-chip">Compte personnel</div>
    </div>

    <div style="margin: 18px 0 22px;">
      <div style="display:flex; justify-content:space-between; margin-bottom:8px; font-weight:600;">
        <span>Avancement</span>
        <span><?= esc((string) $profileCompletion) ?>%</span>
      </div>
      <div style="height: 12px; background:#e8edf2; border-radius:999px; overflow:hidden;">
        <div style="height:100%; width: <?= esc((string) $profileCompletion) ?>%; background: linear-gradient(90deg, #1a6b45, #7bc67e); border-radius:999px;"></div>
      </div>
    </div>

    <form method="post" action="<?= esc($formAction) ?>">
      <?= csrf_field() ?>
      <div class="metric-grid">
        <div class="metric-card">
          <label for="prenom">Prénom</label>
          <input type="text" id="prenom" name="prenom" value="<?= esc(old('prenom', (string) ($userProfile['prenom'] ?? ''))) ?>" required>
        </div>
        <div class="metric-card">
          <label for="nom">Nom</label>
          <input type="text" id="nom" name="nom" value="<?= esc(old('nom', (string) ($userProfile['nom'] ?? ''))) ?>" required>
        </div>
        <div class="metric-card">
          <label for="email">Email</label>
          <input type="email" id="email" name="email" value="<?= esc(old('email', (string) ($userProfile['email'] ?? ''))) ?>" required>
        </div>
        <div class="metric-card">
          <label for="genre">Genre</label>
          <select id="genre" name="genre" required>
            <?php $selectedGenre = old('genre', (string) ($userProfile['genre'] ?? '')); ?>
            <option value="M" <?= $selectedGenre === 'M' ? 'selected' : '' ?>>Masculin</option>
            <option value="F" <?= $selectedGenre === 'F' ? 'selected' : '' ?>>Féminin</option>
          </select>
        </div>
        <div class="metric-card">
          <label for="taille">Taille</label>
          <div class="input-unit">
            <input type="number" id="taille" name="taille" value="<?= esc(old('taille', (string) ($userProfile['taille'] ?? '170'))) ?>" min="50" required oninput="updateIMCPreview()">
            <span class="unit">cm</span>
          </div>
        </div>
        <div class="metric-card">
          <label for="poids">Poids</label>
          <div class="input-unit">
            <input type="number" id="poids" name="poids" value="<?= esc(old('poids', (string) ($userProfile['poids'] ?? '70'))) ?>" min="20" step="0.1" required oninput="updateIMCPreview()">
            <span class="unit">kg</span>
          </div>
        </div>
      </div>

      <div class="imc-preview" style="margin-top:18px;">
        <div>
          <div class="imc-caption">Aperçu IMC</div>
          <div class="imc-status" id="imc-status">Calcul en cours</div>
        </div>
        <div class="imc-value" id="imc-val"><?= esc(number_format((float) ($userProfile['imc'] ?? 0), 1, ',', ' ')) ?></div>
      </div>

      <div class="notice" style="margin-top:16px;">
        Les champs manquants actuellement: <?= esc(! empty($missingFields) ? implode(', ', $missingFields) : 'aucun') ?>.
      </div>

      <div class="footer">
        <a class="btn-back" href="<?= esc(site_url('dashboard')) ?>">← Retour tableau de bord</a>
        <button class="btn-submit" type="submit">Enregistrer le profil</button>
      </div>
    </form>
  </div>

  <div class="card">
    <div class="dashboard-card-head">
      <div>
        <div class="dashboard-kicker">Résumé</div>
        <h3>État du profil</h3>
      </div>
      <div class="dashboard-chip">Données actives</div>
    </div>

    <div class="history-list premium-list" style="margin-top: 10px;">
      <article class="history-item premium-item">
        <div class="history-main">
          <div class="history-topline">
            <div class="history-name">Identité</div>
          </div>
          <div class="history-desc"><?= esc(trim((string) ($userProfile['prenom'] ?? '') . ' ' . (string) ($userProfile['nom'] ?? ''))) ?></div>
          <div class="history-meta"><span><?= esc((string) ($userProfile['email'] ?? '')) ?></span><span><?= esc((string) ($userProfile['genre'] ?? '')) ?></span></div>
        </div>
      </article>

      <article class="history-item premium-item">
        <div class="history-main">
          <div class="history-topline">
            <div class="history-name">Santé</div>
          </div>
          <div class="history-desc">Taille: <?= esc((string) ($userProfile['taille'] ?? 0)) ?> cm, poids: <?= esc(number_format((float) ($userProfile['poids'] ?? 0), 1, ',', ' ')) ?> kg</div>
          <div class="history-meta"><span>IMC <?= esc(number_format((float) ($userProfile['imc'] ?? 0), 1, ',', ' ')) ?></span><span>Complétion <?= esc((string) $profileCompletion) ?>%</span></div>
        </div>
      </article>
    </div>

    <div class="wallet-note" style="margin-top: 16px;">
      Cette page alimente le tableau de bord et recalcule automatiquement l’IMC après enregistrement.
    </div>
  </div>
</div>

<script>
function updateIMCPreview() {
  const taille = parseFloat(document.getElementById('taille').value) / 100;
  const poids = parseFloat(document.getElementById('poids').value);

  if (!taille || !poids) {
    return;
  }

  const imc = (poids / (taille * taille)).toFixed(1);
  const value = parseFloat(imc);
  const status = value < 18.5 ? 'Insuffisance pondérale' : value < 25 ? 'Poids normal' : value < 30 ? 'Surpoids' : 'Obésité';

  document.getElementById('imc-val').textContent = imc;
  document.getElementById('imc-status').textContent = status;
}

updateIMCPreview();
</script>

<?= $this->endSection() ?>