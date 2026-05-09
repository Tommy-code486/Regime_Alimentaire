<?php

namespace App\Models;

use CodeIgniter\Model;

class PaiementModel extends Model
{
    protected $table = 'paiements';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useAutoIncrement = false;
    protected $allowedFields = ['id', 'user_id', 'souscription_id', 'montant', 'id_type_paiement', 'created_at'];

    public function nextId(): int
    {
        $row = $this->select('MAX(id) as max_id')->first();
        $max = is_array($row) ? (int) ($row['max_id'] ?? 0) : 0;

        return $max + 1;
    }

    public function getTypeIdByName(string $name): ?int
    {
        $row = $this->db->table('type_paiments')
            ->select('id')
            ->where('nom', trim($name))
            ->get()
            ->getRowArray();

        if (! is_array($row)) {
            return null;
        }

        return (int) ($row['id'] ?? 0) ?: null;
    }
}
