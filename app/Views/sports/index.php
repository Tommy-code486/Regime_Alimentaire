<?= $this->extend('layouts/app') ?>

<?php
$errors = $errors ?? [];
$sports = $sports ?? [];
$sport = $sport ?? [
    'id' => '',
    'nom' => '',
    'description' => '',
    'calories_par_heure' => '',
    'actif' => 1,
];
$isEdit = $isEdit ?? false;
$action = $action ?? site_url('sports/store');

$message = session()->getFlashdata('message');
$messageType = session()->getFlashdata('message_type') ?? 'success';

$nomValue = old('nom', $sport['nom']);
$descValue = old('description', $sport['description']);
$calValue = old('calories_par_heure', $sport['calories_par_heure']);
$actifValue = old('actif', (string) $sport['actif']);

$total = count($sports);
$editingId = $isEdit ? (int) ($sport['id'] ?? 0) : 0;
$formTitle = $isEdit ? 'Modifier l\'activite' : 'Nouvelle activite';
$formMode = $isEdit ? 'Edition' : 'Ajout';
$submitLabel = $isEdit ? 'Enregistrer' : 'Ajouter';
$isActive = $actifValue === '1';
?>

<?= $this->section('content') ?>
    <p class="intro">Gestion des activites sportives - <strong><?= esc($total) ?> activite<?= $total > 1 ? 's' : '' ?> enregistree<?= $total > 1 ? 's' : '' ?></strong></p>

    <?php if ($message): ?>
        <div class="alert alert-<?= $messageType === 'error' ? 'error' : 'success' ?>">
            <span><?= esc($message) ?></span>
        </div>
    <?php endif; ?>

    <div class="regime-form-layout">
        <div class="form-panel">
            <div class="form-header">
                <div>
                    <div class="card-kicker"><?= esc($formMode) ?></div>
                    <h2 class="form-title"><?= esc($formTitle) ?></h2>
                </div>
                <a class="btn-export" href="<?= esc(site_url('admin/dashboard')) ?>">← Retour</a>
            </div>

            <form method="post" action="<?= esc($action) ?>" class="regime-form">
                <?= csrf_field() ?>
                <?php if ($isEdit): ?>
                    <input type="hidden" name="id" value="<?= esc($sport['id']) ?>">
                <?php endif; ?>

                <?php if (! empty($errors)): ?>
                    <div class="form-errors">
                        <ul>
                            <?php foreach ($errors as $error): ?>
                                <li><?= esc($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <div class="field">
                    <label for="nom">Nom <span class="required">*</span></label>
                    <input id="nom" type="text" name="nom"
                        placeholder="Ex : Course a pied" maxlength="100" required
                        value="<?= esc($nomValue) ?>">
                    <span class="form-hint">Maximum 100 caracteres</span>
                </div>

                <div class="field">
                    <label for="description">Description</label>
                    <textarea id="description" name="description"
                        placeholder="Decrivez cette activite, ses bienfaits..." maxlength="1000" rows="4"><?= esc($descValue) ?></textarea>
                </div>

                <div class="card-grid cols-2">
                    <div class="field">
                        <label for="calories_par_heure">Calories brulees par heure <span class="required">*</span></label>
                        <div class="input-with-unit">
                            <input id="calories_par_heure" type="number" name="calories_par_heure"
                                placeholder="550" min="50" max="1500" required
                                value="<?= esc($calValue) ?>">
                            <span class="input-unit">kcal/h</span>
                        </div>
                        <span class="form-hint">Entre 50 et 1500 kcal/heure</span>
                    </div>
                </div>

                <label class="toggle-row">
                    <input type="hidden" name="actif" value="0">
                    <input type="checkbox" name="actif" value="1" <?= $isActive ? 'checked' : '' ?>>
                    <span>Activite active</span>
                </label>

                <div class="field-group">
                    <a class="btn" href="<?= esc(site_url('sports')) ?>">Annuler</a>
                    <button type="submit" class="btn btn-primary"><?= esc($submitLabel) ?></button>
                </div>
            </form>
        </div>
    </div>

    <div class="sports-grid">
        <?php if (! empty($sports)): ?>
            <?php foreach ($sports as $row): ?>
                <?php
                $rowId = (int) ($row['id'] ?? 0);
                $rowName = (string) ($row['nom'] ?? '');
                $rowDesc = (string) ($row['description'] ?? '');
                $rowCalories = (int) ($row['calories_par_heure'] ?? 0);
                $rowActive = (int) ($row['actif'] ?? 0) === 1;
                // Emojis pour différentes activités
                $icons = ['🏃', '🚴', '⛹️', '🏊', '🧘', '🤸', '🏋️', '🚶', '🧗', '🤾'];
                $icon = $icons[$rowId % count($icons)];
                ?>
                <div class="sport-card <?= $rowActive ? '' : 'inactive' ?>">
                    <?php if (! $rowActive): ?>
                        <div class="badge-inactive-card">Inactif</div>
                    <?php endif; ?>
                    <div class="sport-top"><?= $icon ?></div>
                    <div class="sport-body">
                        <div class="sport-name"><?= esc($rowName) ?></div>
                        <?php if ($rowDesc): ?>
                            <div class="sport-desc"><?= esc($rowDesc) ?></div>
                        <?php endif; ?>
                        <div class="sport-stats">
                            <div class="stat-item">
                                <span class="stat-label">Calories/h</span>
                                <span class="stat-value"><?= esc($rowCalories) ?></span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-label">Statut</span>
                                <span class="badge <?= $rowActive ? 'badge-green' : 'badge-gray' ?>">
                                    <?= $rowActive ? 'Actif' : 'Inactif' ?>
                                </span>
                            </div>
                        </div>
                        <div class="sport-footer">
                            <a class="btn-export" href="<?= esc(site_url('sports/edit/' . $rowId)) ?>">Modifier</a>
                            <form action="<?= esc(site_url('sports/delete/' . $rowId)) ?>" method="post" onsubmit="return confirm('Supprimer cette activite ?');">
                                <?= csrf_field() ?>
                                <button class="btn-danger" type="submit">Supprimer</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div style="grid-column: 1 / -1; text-align: center; padding: 40px;">
                <p class="muted">Aucune activite sportive enregistree.</p>
                <p class="muted" style="font-size: 12px;">Ajoutez une nouvelle activite avec le formulaire ci-dessus.</p>
            </div>
        <?php endif; ?>
    </div>

<?= $this->endSection() ?>
