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
