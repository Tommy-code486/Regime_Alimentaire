<?= $this->extend('layouts/auth') ?>

<?= $this->section('styles') ?>
.steps { display: grid; grid-template-columns: 1fr auto 1fr; align-items: center; gap: 14px; margin-bottom: 26px; }
.step { display: flex; flex-direction: column; align-items: center; }
.step-circle {
  width: 38px;
  height: 38px;
  border-radius: 999px;
  display: grid;
  place-items: center;
  font-weight: 800;
  background: #edf5ef;
  color: #6b8576;
}
.step.active .step-circle,
.step.done .step-circle { background: #1a6b45; color: white; }
.step-label { font-size: 12px; margin-top: 8px; color: #5f7667; font-weight: 600; }
.step.active .step-label,
.step.done .step-label { color: #1a6b45; }
.step-line { height: 2px; width: 100%; border-radius: 999px; background: rgba(18, 56, 35, 0.12); }
.step-line.done { background: #1a6b45; }
.grid2 { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 14px; }
.field { display: flex; flex-direction: column; gap: 6px; margin-bottom: 14px; }
.field label { font-size: 13px; font-weight: 700; color: #385547; }
.field input,
.field select {
  width: 100%;
  border: 1px solid rgba(18, 56, 35, 0.12);
  border-radius: 16px;
  padding: 13px 15px;
  background: #f8fcf9;
  color: #123823;
  font-size: 15px;
}
.field input:focus,
.field select:focus { outline: none; border-color: #1a6b45; box-shadow: 0 0 0 4px rgba(26, 107, 69, 0.12); }
.genre-group { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px; }
.genre-btn {
  border: 1px solid rgba(18, 56, 35, 0.12);
  border-radius: 16px;
  padding: 12px 14px;
  background: #f8fcf9;
  color: #385547;
  font-size: 14px;
  font-weight: 600;
  text-align: center;
  cursor: pointer;
}
.genre-btn.selected { border-color: #1a6b45; background: #e7f3ec; color: #1a6b45; }
.footer { display: flex; justify-content: space-between; align-items: center; gap: 12px; margin-top: 24px; }
.btn-next {
  border: none;
  border-radius: 16px;
  padding: 13px 22px;
  background: linear-gradient(135deg, #1a6b45, #145438);
  color: white;
  font-size: 15px;
  font-weight: 700;
  cursor: pointer;
}
.login-link { margin-top: 18px; text-align: center; font-size: 13px; color: #5f7667; }
.login-link a { color: #1a6b45; font-weight: 700; }
<?= $this->endSection() ?>

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

  <div class="grid2">
    <div class="field">
      <label for="date_naissance">Date de naissance</label>
      <input id="date_naissance" name="date_naissance" type="date" value="<?= esc(old('date_naissance', $registrationStep1['date_naissance'] ?? '')) ?>">
    </div>
    <div class="field">
      <label for="telephone">Téléphone</label>
      <input id="telephone" name="telephone" type="tel" value="<?= esc(old('telephone', $registrationStep1['telephone'] ?? '')) ?>" placeholder="+261 34 00 000 00">
    </div>
  </div>

  <div class="field">
    <label>Genre</label>
    <div class="genre-group">
      <label class="genre-btn <?= (old('genre', $registrationStep1['genre'] ?? 'M') === 'M') ? 'selected' : '' ?>">
        <input type="radio" name="genre" value="M" <?= (old('genre', $registrationStep1['genre'] ?? 'M') === 'M') ? 'checked' : '' ?> hidden>
        Homme
      </label>
      <label class="genre-btn <?= (old('genre', $registrationStep1['genre'] ?? '') === 'F') ? 'selected' : '' ?>">
        <input type="radio" name="genre" value="F" <?= (old('genre', $registrationStep1['genre'] ?? '') === 'F') ? 'checked' : '' ?> hidden>
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
<?= $this->endSection() ?>