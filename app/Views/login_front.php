<?= $this->extend('layouts/auth') ?>

<?= $this->section('content') ?>
<div class="card-kicker">Connexion sécurisée</div>
<h1 class="auth-title">Connexion</h1>
<p class="auth-subtitle">Accédez à votre espace utilisateur ou administrateur avec vos identifiants.</p>

<form class="auth-form" action="<?= esc(site_url('login')) ?>" method="post">
  <?= csrf_field() ?>
  <div class="field">
    <label for="email">Adresse email</label>
    <input id="email" name="email" type="email" value="<?= esc(old('email')) ?>" placeholder="exemple@email.com" required>
  </div>

  <div class="field">
    <label for="mot_de_passe">Mot de passe</label>
    <input id="mot_de_passe" name="mot_de_passe" type="password" placeholder="••••••••" required>
  </div>

  <div class="forgot-row">
    <span>Mot de passe oublié ?</span>
    <a href="#">Réinitialiser</a>
  </div>

  <button class="btn-primary" type="submit">Se connecter</button>

  <div class="divider"><span>ou</span></div>

  <div class="register-link">
    <span>Pas encore inscrit ?</span>
    <a href="<?= esc(site_url('register')) ?>">Créer un compte</a>
  </div>
</form>
<?= $this->endSection() ?>