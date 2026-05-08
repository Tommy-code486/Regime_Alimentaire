<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>

<div class="admin-stats-layout">
<div class="stats-top-cards">
  <div class="stat-card"><div class="lbl">Utilisateurs</div><div class="val"><?= esc((string) ($totals['users'] ?? 0)) ?></div><div class="sub">Comptes</div></div>
  <div class="stat-card"><div class="lbl">Revenu total</div><div class="val"><?= esc(number_format((float) ($totals['revenue'] ?? 0), 0, ',', ' ')) ?> Ar</div><div class="sub">Toutes sources</div></div>
  <div class="stat-card"><div class="lbl">Souscriptions</div><div class="val"><?= esc((string) ($totals['subscriptions'] ?? 0)) ?></div><div class="sub">Actuelles</div></div>
</div>

<div class="charts-grid">
  <div class="card chart-card">
    <h3>Répartition utilisateurs par genre</h3>
    <div class="chart-shell">
      <canvas id="chartGender"></canvas>
    </div>
  </div>

  <div class="card chart-card">
    <h3>Inscriptions (6 derniers mois)</h3>
    <div class="chart-shell">
      <canvas id="chartRegistrations"></canvas>
    </div>
  </div>

  <div class="card chart-card">
    <h3>Revenu mensuel (6 derniers mois)</h3>
    <div class="chart-shell">
      <canvas id="chartRevenue"></canvas>
    </div>
  </div>

  <div class="card chart-card">
    <h3>Revenu par objectif</h3>
    <div class="chart-shell">
      <canvas id="chartObj"></canvas>
    </div>
  </div>
</div>

<div class="card table-card">
  <h3>Top régimes (par nombre de souscriptions)</h3>
  <table class="table-plain">
    <thead><tr><th>Régime</th><th>Souscriptions</th></tr></thead>
    <tbody>
      <?php foreach (($topRegimes ?? []) as $r) : ?>
        <tr><td><?= esc((string) ($r['regime'] ?? '—')) ?></td><td><?= esc((string) ($r['total'] ?? 0)) ?></td></tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<!-- Chart.js via CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  const genderLabels = <?= $chart_users_gender_labels ?? '[]' ?>;
  const genderData = <?= $chart_users_gender ?? '[]' ?>;

  const regLabels = <?= $chart_months_labels ?? '[]' ?>;
  const regData = <?= $chart_registrations ?? '[]' ?>;

  const revLabels = <?= $chart_revenue_labels ?? '[]' ?>;
  const revData = <?= $chart_revenue ?? '[]' ?>;

  const objLabels = <?= $chart_obj_labels ?? '[]' ?>;
  const objData = <?= $chart_obj_values ?? '[]' ?>;

  const topLabels = <?= $top_regimes_labels ?? '[]' ?>;
  const topValues = <?= $top_regimes_values ?? '[]' ?>;

  function createPie(ctx, labels, data) {
    return new Chart(ctx, { type: 'pie', data: { labels, datasets: [{ data, backgroundColor: ['#1a6b45','#f0c040','#e7f3ec','#c98a11'] }] }, options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } } });
  }

  function createLine(ctx, labels, data) {
    return new Chart(ctx, { type: 'line', data: { labels, datasets: [{ label: 'Valeur', data, fill: true, backgroundColor: 'rgba(26,107,69,0.08)', borderColor: '#1a6b45', tension: 0.35, pointRadius: 2 }] }, options: { responsive: true, maintainAspectRatio: false, scales: { x: { ticks: { maxRotation: 0, autoSkip: true } } } } });
  }

  function createBar(ctx, labels, data) {
    return new Chart(ctx, { type: 'bar', data: { labels, datasets: [{ label: 'Montant', data, backgroundColor: '#f0c040' }] }, options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { x: { ticks: { maxRotation: 0, autoSkip: true } } } } });
  }

  document.addEventListener('DOMContentLoaded', () => {
    const g = document.getElementById('chartGender'); if (g) createPie(g.getContext('2d'), genderLabels, genderData);
    const r = document.getElementById('chartRegistrations'); if (r) createLine(r.getContext('2d'), regLabels, regData);
    const rv = document.getElementById('chartRevenue'); if (rv) createLine(rv.getContext('2d'), revLabels, revData);
    const o = document.getElementById('chartObj'); if (o) createBar(o.getContext('2d'), objLabels, objData);
  });
</script>

</div>

<?= $this->endSection() ?>
