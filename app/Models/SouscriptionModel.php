<?php

namespace App\Models;

use CodeIgniter\Model;

class SouscriptionModel extends Model
{
    protected $table = 'souscriptions';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useAutoIncrement = false;
    protected $allowedFields = [
        'id',
        'user_id',
        'regime_id',
        'prix_regime_id',
        'objectif_choisi',
        'date_debut',
        'date_fin',
        'montant_paye',
    ];

    public function nextId(): int
    {
        $row = $this->select('MAX(id) as max_id')->first();
        $max = is_array($row) ? (int) ($row['max_id'] ?? 0) : 0;

        return $max + 1;
    }

    public function findActiveByUser(int $userId): ?array
    {
        $today = date('Y-m-d');

        $row = $this->select('souscriptions.*, regimes.nom as regime_nom, regimes.duree_semaines as regime_duree')
            ->join('regimes', 'regimes.id = souscriptions.regime_id', 'left')
            ->where('souscriptions.user_id', $userId)
            ->where('souscriptions.date_fin >=', $today)
            ->orderBy('souscriptions.date_fin', 'DESC')
            ->first();

        return is_array($row) ? $row : null;
    }
}
