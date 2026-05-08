<?= $this->extend('layouts/auth') ?>

<?= $this->section('content') ?>
<div class="card-kicker">Inscription en 2 étapes</div>
<h1 class="auth-title">Vos données de santé</h1>
<p class="auth-subtitle">Étape 2 sur 2 — Ces informations permettent de calculer votre IMC.</p>

<div class="steps" aria-hidden="true">
  <div class="step done">
    <div class="step-circle">✓</div>
    <div class="step-label">Profil</div>
  </div>
  <div class="step-line"></div>
  <div class="step active">
    <div class="step-circle">2</div>
    <div class="step-label">Santé</div>
  </div>
</div>

<form action="<?= esc(site_url('register/step2')) ?>" method="post" id="registerForm_2">
  <?= csrf_field() ?>
  <div class="metric-grid">
    <div class="metric-card">
      <label for="taille">Taille</label>
      <div class="input-unit">
        <input type="number" name="taille" id="taille" value="<?= esc(old('taille', '170')) ?>" oninput="calcIMC()" min="50" required>
        <span class="unit">cm</span>
      </div>
    </div>
    <div class="metric-card">
      <label for="poids">Poids actuel</label>
      <div class="input-unit">
        <input type="number" name="poids" id="poids" value="<?= esc(old('poids', '70')) ?>" oninput="calcIMC()" min="20" step="0.1" required>
        <span class="unit">kg</span>
      </div>
    </div>
  </div>

  <div class="imc-preview">
    <div>
      <div class="imc-caption">Votre IMC calculé</div>
      <div class="imc-status" id="imc-status">Poids normal</div>
    </div>
    <div class="imc-value" id="imc-val">24.2</div>
  </div>

  <div class="notice">Vos données de santé sont confidentielles et ne seront jamais partagées.</div>

  <div class="footer">
    <a class="btn-back" href="<?= esc(site_url('register')) ?>">← Retour</a>
    <button class="btn-submit" type="submit">Créer mon compte ✓</button>
  </div>
</form>

<script>
function calcIMC() {
  const t = parseFloat(document.getElementById('taille').value) / 100;
  const p = parseFloat(document.getElementById('poids').value);

  if (t && p) {
    const imc = (p / (t * t)).toFixed(1);
    document.getElementById('imc-val').textContent = imc;

    const value = parseFloat(imc);
    const status = value < 18.5 ? 'Insuffisance pondérale' : value < 25 ? 'Poids normal' : value < 30 ? 'Surpoids' : 'Obésité';
    document.getElementById('imc-status').textContent = status;
  }
}

calcIMC();
</script>
<?= $this->endSection() ?>