<?= $this->extend('layouts/auth') ?>

<?= $this->section('styles') ?>
.auth-form { display: flex; flex-direction: column; gap: 16px; }
.field { display: flex; flex-direction: column; gap: 6px; }
.field label { font-size: 13px; font-weight: 700; color: #385547; }
.field input {
  width: 100%;
  border: 1px solid rgba(18, 56, 35, 0.12);
  border-radius: 16px;
  padding: 14px 16px;
  background: #f8fcf9;
  color: #123823;
  font-size: 15px;
}
.field input:focus { outline: none; border-color: #1a6b45; box-shadow: 0 0 0 4px rgba(26, 107, 69, 0.12); }
.btn-primary {
  display: inline-flex;
  justify-content: center;
  align-items: center;
  width: 100%;
  padding: 14px 18px;
  border: none;
  border-radius: 16px;
  background: linear-gradient(135deg, #1a6b45, #145438);
  color: white;
  font-size: 15px;
  font-weight: 700;
  cursor: pointer;
}
.btn-primary:hover { filter: brightness(1.05); }
.forgot-row,
.register-link {
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 13px;
  color: #5f7667;
}
.forgot-row a,
.register-link a { color: #1a6b45; font-weight: 700; }
.divider {
  display: flex;
  align-items: center;
  gap: 12px;
  color: #5f7667;
  font-size: 12px;
}
.divider::before,
.divider::after { content: ''; height: 1px; flex: 1; background: rgba(18, 56, 35, 0.12); }
<?= $this->endSection() ?>

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