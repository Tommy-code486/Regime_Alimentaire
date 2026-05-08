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
.step-line { height: 2px; width: 100%; border-radius: 999px; background: #1a6b45; }
.metric-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 14px; margin-bottom: 16px; }
.metric-card {
  border: 1px solid rgba(18, 56, 35, 0.12);
  border-radius: 18px;
  padding: 16px;
  text-align: center;
  background: #f8fcf9;
}
.metric-card label { font-size: 12px; color: #5f7667; display: block; margin-bottom: 8px; font-weight: 700; }
.metric-card .input-unit { display: flex; align-items: center; gap: 8px; justify-content: center; }
.metric-card input {
  width: 90px;
  padding: 10px 12px;
  border: 1px solid rgba(18, 56, 35, 0.12);
  border-radius: 14px;
  font-size: 22px;
  font-weight: 800;
  text-align: center;
  background: white;
  color: #123823;
}
.metric-card input:focus { outline: none; border-color: #1a6b45; box-shadow: 0 0 0 4px rgba(26, 107, 69, 0.12); }
.metric-card .unit { font-size: 14px; color: #5f7667; }
.imc-preview {
  background: #e7f3ec;
  border: 1px solid rgba(26, 107, 69, 0.12);
  border-radius: 18px;
  padding: 16px 18px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 20px;
}
.imc-value { font-size: 34px; font-weight: 900; color: #1a6b45; }
.imc-status { font-size: 12px; color: #1a6b45; font-weight: 700; }
.notice {
  font-size: 12px;
  color: #5f7667;
  background: #f7fbf8;
  border: 1px solid rgba(18, 56, 35, 0.08);
  border-radius: 16px;
  padding: 10px 14px;
  margin-bottom: 16px;
}
.footer { display: flex; justify-content: space-between; gap: 12px; margin-top: 24px; }
.btn-back,
.btn-submit {
  border-radius: 16px;
  padding: 13px 20px;
  font-size: 15px;
  font-weight: 700;
}
.btn-back {
  border: 1px solid rgba(18, 56, 35, 0.12);
  background: white;
  color: #385547;
}
.btn-submit {
  border: none;
  background: linear-gradient(135deg, #1a6b45, #145438);
  color: white;
  cursor: pointer;
}
<?= $this->endSection() ?>

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
      <div style="font-size:12px;color:#1a6b45;font-weight:700;">Votre IMC calculé</div>
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