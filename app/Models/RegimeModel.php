<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Models\PrixRegimeModel;

class RegimeModel extends Model
{
    protected $table = 'regimes';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useAutoIncrement = true;
    protected $allowedFields = [
        'nom',
        'description',
        'pourcentage_viande',
        'pourcentage_poisson',
        'pourcentage_volaille',
        'variation_poids',
        'duree_semaines',
        'id_objectif',
        'actif',
    ];

    public function getObjectives(): array
    {
        return $this->db->table('objectifs')->orderBy('id', 'ASC')->get()->getResultArray();
    }

    public function getRegimesWithDetails(): array
    {
        $regimes = $this->select('regimes.*, objectifs.nom as objectif_nom, objectifs.description as objectif_description')
            ->join('objectifs', 'objectifs.id = regimes.id_objectif', 'left')
            ->orderBy('regimes.id', 'DESC')
            ->findAll();

        $priceModel = new PrixRegimeModel();

        foreach ($regimes as &$regime) {
            $regime['prices'] = $priceModel->findByRegime((int) $regime['id']);
        }

        return $regimes;
    }

    public function findWithDetails(int $id): ?array
    {
        $regime = $this->select('regimes.*, objectifs.nom as objectif_nom, objectifs.description as objectif_description')
            ->join('objectifs', 'objectifs.id = regimes.id_objectif', 'left')
            ->find($id);

        if (! is_array($regime)) {
            return null;
        }

        $priceModel = new PrixRegimeModel();
        $regime['prices'] = $priceModel->findByRegime($id);

        return $regime;
    }

    public function getSuggestedByObjectif(?string $objectifNom = null): array
    {
        $builder = $this->select('regimes.*, objectifs.nom as objectif_nom, objectifs.description as objectif_description')
            ->join('objectifs', 'objectifs.id = regimes.id_objectif', 'left')
            ->where('regimes.actif', 1)
            ->orderBy('regimes.id', 'DESC');

        if (is_string($objectifNom) && trim($objectifNom) !== '') {
            $builder->where('objectifs.nom', trim($objectifNom));
        }

        $regimes = $builder->findAll();
        $priceModel = new PrixRegimeModel();

        foreach ($regimes as &$regime) {
            $regime['prices'] = $priceModel->findByRegime((int) $regime['id']);
        }

        return $regimes;
    }

    /**
     * Retourne les régimes basés sur la comparaison entre l'IMC utilisateur et l'IMC cible
     * @param float $userIMC IMC de l'utilisateur
     * @param float $targetIMC IMC cible
     * @return array Les régimes recommandés
     */
    public function getSuggestedByIMCComparison(float $userIMC, float $targetIMC): array
    {
        // Déterminer l'objectif basé sur la comparaison
        $objectifNom = $this->determineObjectifFromIMCComparison($userIMC, $targetIMC);
        return $this->getSuggestedByObjectif($objectifNom);
    }

    /**
     * Détermine l'objectif basé sur la comparaison des IMC
     */
    private function determineObjectifFromIMCComparison(float $userIMC, float $targetIMC): string
    {
        $difference = abs($userIMC - $targetIMC);

        // Si très proche (différence < 1), maintenir
        if ($difference < 1) {
            return 'equilibre';
        }

        // Si IMC utilisateur < IMC cible, augmentation
        if ($userIMC < $targetIMC) {
            return 'augmentation';
        }

        // Si IMC utilisateur > IMC cible, réduction
        return 'reduction';
    }

    public function saveRegime(array $data): int
    {
        $this->insert($data);

        return (int) $this->getInsertID();
    }

    public function updateRegime(int $id, array $data): bool
    {
        return (bool) $this->update($id, $data);
    }

    public function deleteRegime(int $id): bool
    {
        return (bool) $this->delete($id);
    }
}
