<?php

namespace App\Models;

use CodeIgniter\Model;

class PrixRegimeModel extends Model
{
    protected $table = 'prix_regimes';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useAutoIncrement = true;
    protected $allowedFields = ['regime_id', 'duree_semaines', 'prix'];

    public function findByRegime(int $regimeId): array
    {
        return $this->where('regime_id', $regimeId)
            ->orderBy('duree_semaines', 'ASC')
            ->findAll();
    }

    public function syncForRegime(int $regimeId, array $prices): void
    {
        $this->where('regime_id', $regimeId)->delete();

        foreach ($prices as $price) {
            $duration = (int) ($price['duree_semaines'] ?? 0);
            $amount = (float) ($price['prix'] ?? 0);

            if ($duration > 0 && $amount > 0) {
                $this->insert([
                    'regime_id' => $regimeId,
                    'duree_semaines' => $duration,
                    'prix' => $amount,
                ]);
            }
        }
    }
}