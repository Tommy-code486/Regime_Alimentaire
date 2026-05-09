<?php

namespace App\Models;

use CodeIgniter\Model;

class ObjectifModel extends Model
{
    protected $table = 'objectifs';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useAutoIncrement = false;
    protected $allowedFields = ['id', 'nom', 'description'];

    public function allOrdered(): array
    {
        return $this->orderBy('id', 'ASC')->findAll();
    }

    public function findByNom(string $nom): ?array
    {
        $value = trim($nom);
        if ($value === '') {
            return null;
        }

        $row = $this->where('nom', $value)->first();

        return is_array($row) ? $row : null;
    }
}
