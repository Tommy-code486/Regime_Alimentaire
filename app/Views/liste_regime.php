<?= $this->extend('layouts/app') ?>
<?= $this->section('content') ?>
<?php $isAdmin = (bool) ($isAdmin ?? false); ?>

<p class="intro">Basé sur votre objectif : <strong><?= esc($isAdmin ? 'Gestion des régimes' : 'Consulter les régimes') ?></strong> - IMC actuel : <strong><?= esc($imc ?? '24.2') ?></strong></p>

<?php if ($isAdmin) : ?>
	<div class="page-actions">
		<a class="btn-export" href="<?= esc(site_url('regimes/create')) ?>">+ Nouveau régime</a>
	</div>
<?php endif; ?>

<div class="regimes-grid">
	<?php foreach (($regimes ?? []) as $index => $regime) : ?>
		<?php
			$prices = $regime['prices'] ?? [];
			$recommended = (int) ($regime['id_objectif'] ?? 0) === 1 || $index === 0;
			$icon = ((float) ($regime['variation_poids'] ?? 0) < 0) ? '🥗' : (((float) ($regime['variation_poids'] ?? 0) > 0) ? '💪' : '⚖️');
		?>
		<div class="regime-card <?= $recommended ? 'recommended' : '' ?>">
			<?php if ($recommended) : ?>
				<div class="badge-rec">Recommandé</div>
			<?php endif; ?>
			<div class="badge-gold"><?= esc((string) ($regime['objectif_nom'] ?? 'Objectif')) ?></div>
			<div class="regime-top"><?= esc($icon) ?></div>
			<div class="regime-body">
				<div class="regime-name"><?= esc((string) ($regime['nom'] ?? '')) ?></div>
				<div class="regime-desc"><?= esc((string) ($regime['description'] ?? '')) ?></div>
				<div class="macro-pills">
					<span class="macro-pill pill-meat">Viande <?= esc((string) ($regime['pourcentage_viande'] ?? 0)) ?>%</span>
					<span class="macro-pill pill-fish">Poisson <?= esc((string) ($regime['pourcentage_poisson'] ?? 0)) ?>%</span>
					<span class="macro-pill pill-poultry">Volaille <?= esc((string) ($regime['pourcentage_volaille'] ?? 0)) ?>%</span>
				</div>
				<div class="regime-meta">
					<span class="duration-tag">📅 <?= esc((string) ($regime['duree_semaines'] ?? 0)) ?> semaines</span>
					<span class="duration-tag">Variation : <?= esc(number_format((float) ($regime['variation_poids'] ?? 0), 1, ',', ' ')) ?> kg</span>
				</div>
				<div class="price-pills">
					<?php if (! empty($prices)) : ?>
						<?php foreach ($prices as $price) : ?>
							<span class="price-pill"><?= esc((string) $price['duree_semaines']) ?> sem - <?= esc(number_format((float) $price['prix'], 0, ',', ' ')) ?> Ar</span>
						<?php endforeach; ?>
					<?php else : ?>
						<span class="price-pill">Tarif à définir</span>
					<?php endif; ?>
				</div>
				<div class="regime-footer">
					<div>
						<div class="duration-tag">Statut : <?= ((int) ($regime['actif'] ?? 0) === 1) ? 'Actif' : 'Inactif' ?></div>
						<div class="price"><?= ! empty($prices) ? esc(number_format((float) $prices[0]['prix'], 0, ',', ' ')) . ' Ar' : '—' ?></div>
					</div>
					<?php if ($isAdmin) : ?>
						<div class="card-actions">
							<a class="btn-export" href="<?= esc(site_url('regimes/' . $regime['id'] . '/edit')) ?>">Modifier</a>
							<form action="<?= esc(site_url('regimes/' . $regime['id'] . '/delete')) ?>" method="post" onsubmit="return confirm('Supprimer ce régime ?');">
								<?= csrf_field() ?>
								<button class="btn-danger" type="submit">Supprimer</button>
							</form>
						</div>
					<?php else : ?>
						<button class="btn-choose" type="button">Choisir</button>
					<?php endif; ?>
				</div>
			</div>
		</div>
	<?php endforeach; ?>
</div>

<div class="export-bar">
	<button class="btn-export" type="button">Exporter en PDF</button>
	<button class="btn-export" type="button">Imprimer</button>
</div>
<?= $this->endSection() ?>