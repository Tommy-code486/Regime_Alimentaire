<?= $this->extend('layouts/auth') ?>

<?php $selectedGenre = old('genre', $registrationStep1['genre'] ?? ''); ?>

<?= $this->section('content') ?>
<div class="card-kicker">Inscription en 2 étapes</div>
<h1 class="auth-title">Créer votre compte</h1>
<p class="auth-subtitle">Étape 1 sur 2 — Informations personnelles et mot de passe.</p>

<div class="steps" aria-hidden="true">
  <div class="step active">
    <div class="step-circle">1</div>
    <div class="step-label">Profil</div>
  </div>
  <div class="step-line"></div>
  <div class="step">
    <div class="step-circle">2</div>
    <div class="step-label">Santé</div>
  </div>
</div>

<form action="<?= esc(site_url('register/step1')) ?>" method="post">
  <?= csrf_field() ?>
  <div class="grid2">
    <div class="field">
      <label for="prenom">Prénom</label>
      <input id="prenom" name="prenom" type="text" value="<?= esc(old('prenom', $registrationStep1['prenom'] ?? '')) ?>" placeholder="Jean" required>
    </div>
    <div class="field">
      <label for="nom">Nom</label>
      <input id="nom" name="nom" type="text" value="<?= esc(old('nom', $registrationStep1['nom'] ?? '')) ?>" placeholder="Dupont" required>
    </div>
  </div>

  <div class="field">
    <label for="email">Adresse email</label>
    <input id="email" name="email" type="email" value="<?= esc(old('email', $registrationStep1['email'] ?? '')) ?>" placeholder="jean.dupont@email.com" required>
  </div>

  <div class="field">
    <label>Genre</label>
    <div class="genre-group">
      <label class="genre-btn <?= ($selectedGenre === 'M') ? 'selected' : '' ?>">
        <input type="radio" name="genre" value="M" <?= ($selectedGenre === 'M') ? 'checked' : '' ?>>
        Homme
      </label>
      <label class="genre-btn <?= ($selectedGenre === 'F') ? 'selected' : '' ?>">
        <input type="radio" name="genre" value="F" <?= ($selectedGenre === 'F') ? 'checked' : '' ?>>
        Femme
      </label>
    </div>
  </div>

  <div class="field">
    <label for="mot_de_passe">Mot de passe</label>
    <input id="mot_de_passe" name="mot_de_passe" type="password" placeholder="••••••••" required>
  </div>

  <div class="field">
    <label for="mot_de_passe_confirmation">Confirmer le mot de passe</label>
    <input id="mot_de_passe_confirmation" name="mot_de_passe_confirmation" type="password" placeholder="••••••••" required>
  </div>

  <div class="footer">
    <span></span>
    <button class="btn-next" type="submit">Suivant →</button>
  </div>
</form>

<div class="login-link">Déjà inscrit ? <a href="<?= esc(site_url('login')) ?>">Se connecter</a></div>

<script>
document.querySelectorAll('.genre-group input[type="radio"]').forEach((input) => {
  input.addEventListener('change', () => {
    document.querySelectorAll('.genre-group .genre-btn').forEach((label) => {
      const labelInput = label.querySelector('input[type="radio"]');
      label.classList.toggle('selected', Boolean(labelInput && labelInput.checked));
    });
  });
});
</script>
<?= $this->endSection() ?>