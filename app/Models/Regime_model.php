<?php

namespace App\Models;

use CodeIgniter\Model;
use Config\Database;

class Regime_model extends Model
{
    protected $table = 'regimes';
    protected $returnType = 'array';

    public function get_regimes_complets(string $nom_objectif): array
    {
        $db = $this->db ?? Database::connect();

        $builder = $db->table('regimes r');
        $builder->select('r.*, o.nom as objectif_nom');
        $builder->join('objectifs o', 'r.id_objectif = o.id');
        $builder->where('o.nom', $nom_objectif);
        $builder->where('r.actif', 1);

        $regimes = $builder->get()->getResultArray();

        foreach ($regimes as &$r) {
            $tarifsBuilder = $db->table('prix_regimes');
            $tarifsBuilder->where('regime_id', $r['id']);
            $tarifsBuilder->orderBy('duree_semaines', 'ASC');
            $r['tarifs'] = $tarifsBuilder->get()->getResultArray();
        }

        return $regimes;
    }
}