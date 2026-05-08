<?= $this->extend('layouts/app') ?>

<?= $this->section('styles') ?>
.admin-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 16px; margin-bottom: 18px; }
.admin-card {
  background: white;
  border: 1px solid rgba(18, 56, 35, 0.12);
  border-radius: 22px;
  padding: 18px;
  box-shadow: var(--shadow);
}
.admin-card .label { font-size: 12px; color: #5f7667; margin-bottom: 8px; }
.admin-card .value { font-size: 28px; font-weight: 900; color: #1a6b45; letter-spacing: -0.03em; }
.admin-card .note { font-size: 12px; color: #5f7667; margin-top: 3px; }
.admin-panels { display: grid; grid-template-columns: 1.2fr 0.8fr; gap: 16px; }
.list-card {
  background: white;
  border: 1px solid rgba(18, 56, 35, 0.12);
  border-radius: 22px;
  padding: 20px;
  box-shadow: var(--shadow);
}
.list-card h3 { margin: 0 0 14px; font-size: 16px; letter-spacing: -0.02em; }
.activity {
  display: flex;
  align-items: flex-start;
  gap: 12px;
  padding: 12px 0;
  border-bottom: 1px solid rgba(18, 56, 35, 0.08);
}
.activity:last-child { border-bottom: none; }
.activity .badge {
  width: 34px;
  height: 34px;
  border-radius: 12px;
  display: grid;
  place-items: center;
  background: #e7f3ec;
  color: #1a6b45;
  font-weight: 900;
}
.activity .title { font-weight: 800; margin-bottom: 3px; }
.activity .meta { font-size: 12px; color: #5f7667; line-height: 1.45; }

@media (max-width: 1200px) {
  .admin-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
  .admin-panels { grid-template-columns: 1fr; }
}

@media (max-width: 680px) {
  .admin-grid { grid-template-columns: 1fr; }
}
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="admin-grid">
  <div class="admin-card"><div class="label">Utilisateurs</div><div class="value">128</div><div class="note">Comptes actifs</div></div>
  <div class="admin-card"><div class="label">Admins</div><div class="value">2</div><div class="note">Supervision</div></div>
  <div class="admin-card"><div class="label">Abonnements Gold</div><div class="value">64</div><div class="note">Taux de conversion</div></div>
  <div class="admin-card"><div class="label">Paiements</div><div class="value">8.4M</div><div class="note">Ariary collectés</div></div>
</div>

<div class="admin-panels">
  <div class="list-card">
    <h3>Activités récentes</h3>
    <div class="activity">
      <div class="badge">U</div>
      <div>
        <div class="title">Nouvelle inscription</div>
        <div class="meta">Une demande de compte utilisateur a été validée il y a 5 minutes.</div>
      </div>
    </div>
    <div class="activity">
      <div class="badge">G</div>
      <div>
        <div class="title">Upgrade Gold</div>
        <div class="meta">Un membre a activé l’option Gold et a payé la différence via le portefeuille.</div>
      </div>
    </div>
    <div class="activity">
      <div class="badge">P</div>
      <div>
        <div class="title">Paiement confirmé</div>
        <div class="meta">Un règlement régime a été enregistré avec succès sur la base existante.</div>
      </div>
    </div>
  </div>

  <div class="list-card">
    <h3>Etat de la plateforme</h3>
    <div class="info-card" style="margin-bottom: 12px;">
      <div class="label">Disponibilité</div>
      <div class="value">99.9%</div>
      <div class="note">Services applicatifs en ligne</div>
    </div>
    <div class="info-card">
      <div class="label">Prochain contrôle</div>
      <div class="value" style="font-size:24px;">Aujourd’hui</div>
      <div class="note">Surveillance des inscriptions et paiements</div>
    </div>
  </div>
</div>
<?= $this->endSection() ?>