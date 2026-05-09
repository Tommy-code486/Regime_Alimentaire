<?= $this->extend('layouts/app') ?>
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
    <div class="info-card info-card-spaced">
      <div class="label">Disponibilité</div>
      <div class="value">99.9%</div>
      <div class="note">Services applicatifs en ligne</div>
    </div>
    <div class="info-card">
      <div class="label">Prochain contrôle</div>
      <div class="value value-lg">Aujourd’hui</div>
      <div class="note">Surveillance des inscriptions et paiements</div>
    </div>
  </div>

  <div class="list-card">
    <h3>Gestion sportive</h3>
    <div class="activity">
      <div class="badge">S</div>
      <div>
        <div class="title">Activites sportives</div>
        <div class="meta">Ajouter, modifier ou desactiver les activites proposees.</div>
        <a class="btn-export" href="<?= esc(site_url('sports')) ?>">Gerer les activites</a>
      </div>
    </div>
  </div>
</div>
<?= $this->endSection() ?>