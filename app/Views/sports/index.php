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
$title = $title ?? 'Activites sportives';

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
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= esc(base_url('css/sports.css')) ?>">
</head>
<body>
    <div class="page">
        <div class="page-body">
            <h1 class="page-title">Activites sportives</h1>

            <?php if ($message): ?>
                <div class="flash <?= $messageType === 'error' ? 'flash-error' : 'flash-success' ?>">
                    <span><?= esc($message) ?></span>
                </div>
            <?php endif; ?>

            <div class="content-grid">
                <div class="form-card">
                    <div class="form-card-header">
                        <h3><?= esc($formTitle) ?></h3>
                        <span class="mode-badge"><?= esc($formMode) ?></span>
                    </div>

                    <div class="form-card-body">
                        <?php if (! empty($errors)): ?>
                            <div class="form-errors">
                                <ul>
                                    <?php foreach ($errors as $error): ?>
                                        <li><?= esc($error) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <form method="post" action="<?= esc($action) ?>">
                            <?= csrf_field() ?>
                            <?php if (! $isEdit): ?>
                                <input type="hidden" name="id" value="<?= esc(old('id', $sport['id'])) ?>">
                            <?php endif; ?>

                            <div class="form-group">
                                <label class="form-label">Nom <span class="required">*</span></label>
                                <input type="text" class="form-input" name="nom"
                                    placeholder="Ex : Course a pied" maxlength="100" required
                                    value="<?= esc($nomValue) ?>">
                                <span class="form-hint">Maximum 100 caracteres</span>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Description</label>
                                <textarea class="form-textarea" name="description"
                                    placeholder="Decrivez cette activite, ses bienfaits..." maxlength="1000"><?= esc($descValue) ?></textarea>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Calories brulees par heure <span class="required">*</span></label>
                                <div class="input-with-unit">
                                    <input type="number" class="form-input" name="calories_par_heure"
                                        placeholder="550" min="50" max="1500" required
                                        value="<?= esc($calValue) ?>">
                                    <span class="input-unit">kcal/h</span>
                                </div>
                                <span class="form-hint">Entre 50 et 1500 kcal/heure</span>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Statut</label>
                                <div class="toggle-wrap">
                                    <input type="hidden" name="actif" value="0">
                                    <label class="toggle-switch">
                                        <input class="toggle-input" type="checkbox" name="actif" value="1" <?= $isActive ? 'checked' : '' ?>>
                                        <span class="toggle-slider"></span>
                                    </label>
                                    <span class="toggle-label">Actif</span>
                                </div>
                            </div>

                            <div class="form-actions">
                                <a class="btn btn-outline" href="<?= esc(site_url('sports')) ?>">Annuler</a>
                                <button type="submit" class="btn btn-primary"><?= esc($submitLabel) ?></button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="table-card">
                    <div class="table-header">
                        <h3>Liste des activites</h3>
                        <span class="table-count"><?= esc($total) ?> activite<?= $total > 1 ? 's' : '' ?></span>
                    </div>

                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Activite</th>
                                <th>Calories/heure</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($sports)): ?>
                                <tr class="empty-row">
                                    <td colspan="4">Aucune activite trouvee.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($sports as $row): ?>
                                    <?php
                                    $rowId = (int) ($row['id'] ?? 0);
                                    $rowName = (string) ($row['nom'] ?? '');
                                    $rowDesc = (string) ($row['description'] ?? '');
                                    $rowCalories = (int) ($row['calories_par_heure'] ?? 0);
                                    $rowActive = (int) ($row['actif'] ?? 0) === 1;
                                    ?>
                                    <tr class="<?= $editingId === $rowId ? 'editing' : '' ?>">
                                        <td>
                                            <div class="activity-name"><?= esc($rowName) ?></div>
                                            <div class="activity-desc"><?= esc($rowDesc ?: '-') ?></div>
                                        </td>
                                        <td>
                                            <span class="cal-badge"><?= esc($rowCalories) ?> kcal/h</span>
                                        </td>
                                        <td>
                                            <span class="badge <?= $rowActive ? 'badge-active' : 'badge-inactive' ?>">
                                                <?= $rowActive ? 'Active' : 'Inactive' ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="row-actions">
                                                <a class="btn btn-outline btn-sm" href="<?= esc(site_url('sports/edit/' . $rowId)) ?>">Modifier</a>
                                                <form method="post" action="<?= esc(site_url('sports/delete/' . $rowId)) ?>" onsubmit="return confirm('Supprimer cette activite ?');">
                                                    <?= csrf_field() ?>
                                                    <button class="btn btn-danger btn-sm" type="submit">Supprimer</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
