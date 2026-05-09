<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title><?= esc($title) ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body { font-family: Arial, sans-serif; background: #f6f7fb; margin: 0; }
        .container { max-width: 720px; margin: 40px auto; background: #fff; padding: 24px; border-radius: 8px; box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08); }
        h1 { margin-top: 0; }
        .field { margin-bottom: 14px; }
        label { display: block; font-weight: bold; margin-bottom: 6px; }
        input, textarea, select { width: 100%; padding: 10px; border: 1px solid #ccd1dd; border-radius: 6px; font-size: 14px; }
        textarea { min-height: 120px; resize: vertical; }
        .actions { display: flex; gap: 10px; }
        .btn { background: #2d6cdf; color: #fff; border: none; padding: 10px 14px; border-radius: 6px; cursor: pointer; font-weight: bold; }
        .btn-secondary { background: #666; color: #fff; text-decoration: none; display: inline-flex; align-items: center; padding: 10px 14px; border-radius: 6px; }
        .errors { background: #fdecea; color: #b92b27; padding: 10px 12px; border-radius: 6px; margin-bottom: 16px; }
        .errors ul { margin: 0; padding-left: 18px; }
    </style>
</head>
<>
    <?= $this->extend('layouts/app') ?>
    <?= $this->section('content') ?>
    <div class="container">
        <h1><?= esc($title) ?></h1>

        <?php if (! empty($errors)): ?>
            <div class="errors">
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
                <div class="field">
                    <label for="id">ID</label>
                    <input type="number" id="id" name="id" value="<?= esc(old('id', $sport['id'])) ?>" min="1" required>
                </div>
            <?php else: ?>
                <div class="field">
                    <label for="id">ID</label>
                    <input type="number" id="id" value="<?= esc($sport['id']) ?>" disabled>
                </div>
            <?php endif; ?>

            <div class="field">
                <label for="nom">Nom</label>
                <input type="text" id="nom" name="nom" value="<?= esc(old('nom', $sport['nom'])) ?>" maxlength="100" required>
            </div>

            <div class="field">
                <label for="description">Description</label>
                <textarea id="description" name="description" maxlength="1000"><?= esc(old('description', $sport['description'])) ?></textarea>
            </div>

            <div class="field">
                <label for="calories_par_heure">Calories par heure</label>
                <input type="number" id="calories_par_heure" name="calories_par_heure" value="<?= esc(old('calories_par_heure', $sport['calories_par_heure'])) ?>" min="0" required>
            </div>

            <div class="field">
                <?php $actifValue = old('actif', (string) $sport['actif']); ?>
                <label for="actif">Actif</label>
                <select id="actif" name="actif" required>
                    <option value="1" <?= $actifValue === '1' ? 'selected' : '' ?>>Oui</option>
                    <option value="0" <?= $actifValue === '0' ? 'selected' : '' ?>>Non</option>
                </select>
            </div>

            <div class="actions">
                <button class="btn" type="submit"><?= $isEdit ? 'Mettre a jour' : 'Enregistrer' ?></button>
                <a class="btn-secondary" href="<?= esc(site_url('sports')) ?>">Retour</a>
            </div>
        </form>
    </div>
    <?php $this->endSection() ?>
</body>
</html>
