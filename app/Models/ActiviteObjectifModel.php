<?php

namespace App\Models;

use CodeIgniter\Model;

class ActiviteObjectifModel extends Model
{
    protected $table = 'activites_objectifs';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useAutoIncrement = true;
    protected $allowedFields = ['activite_id', 'objectif_id', 'niveau_priorite'];
    protected $useTimestamps = false;

    /**
     * Obtient les activités sportives recommandées pour un objectif spécifique
     */
    public function getActivitesByObjectif(int $objectifId, int $limit = null): array
    {
        $query = $this->select('activites_objectifs.*, activites_sportives.nom, activites_sportives.description, activites_sportives.calories_par_heure')
            ->join('activites_sportives', 'activites_sportives.id = activites_objectifs.activite_id', 'left')
            ->where('activites_objectifs.objectif_id', $objectifId)
            ->where('activites_sportives.actif', 1)
            ->orderBy('activites_objectifs.niveau_priorite', 'ASC');

        if ($limit !== null && $limit > 0) {
            $query->limit($limit);
        }

        return $query->findAll() ?: [];
    }

    /**
     * Obtient les activités recommandées basées sur la comparaison IMC
     */
    public function getActivitesByIMCComparison(float $userIMC, float $targetIMC, int $limit = null): array
    {
        $objectifId = $this->determineObjectifFromIMC($userIMC, $targetIMC);
        return $this->getActivitesByObjectif($objectifId, $limit);
    }

    /**
     * Retourne les priorites actuellement en base pour une activite
     * @return array assoc tableau [objectif_id => niveau_priorite]
     */
    public function getPrioritiesForActivite(int $activiteId): array
    {
        $rows = $this->where('activite_id', $activiteId)->findAll() ?: [];
        $map = [];
        foreach ($rows as $r) {
            $map[(int) ($r['objectif_id'] ?? 0)] = (int) ($r['niveau_priorite'] ?? 0);
        }

        return $map;
    }

    /**
     * Synchronise les priorites pour une activite : supprime les anciennes et insert les nouvelles
     * @param int $activiteId
     * @param array $priorities assoc [objectif_id => niveau_priorite]
     */
    public function syncForActivite(int $activiteId, array $priorities): void
    {
        // Supprimer existants
        $this->where('activite_id', $activiteId)->delete();

        $rows = [];
        foreach ($priorities as $objectifId => $niveau) {
            $niveauInt = (int) $niveau;
            if ($niveauInt <= 0) {
                continue; // 0 = non associée
            }

            $rows[] = [
                'activite_id' => $activiteId,
                'objectif_id' => (int) $objectifId,
                'niveau_priorite' => $niveauInt,
            ];
        }

        if (! empty($rows)) {
            $this->insertBatch($rows);
        }
    }

    /**
     * Détermine l'objectif basé sur la comparaison des IMC
     * Retourne l'ID de l'objectif: 1=reduction, 2=augmentation, 3=equilibre
     */
    private function determineObjectifFromIMC(float $userIMC, float $targetIMC): int
    {
        if ($userIMC < $targetIMC - 1) {
            return 2; // augmentation
        } elseif ($userIMC > $targetIMC + 1) {
            return 1; // reduction
        } else {
            return 3; // equilibre
        }
    }
}
