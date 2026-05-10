<?php

namespace App\Models;

use CodeIgniter\Model;

class IMCCategoryModel extends Model
{
    protected $table = 'categories_imc';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useAutoIncrement = true;
    protected $allowedFields = ['nom', 'imc_min', 'imc_max', 'description', 'ordre'];
    protected $useTimestamps = false;

    /**
     * Retourne toutes les catégories IMC ordonnées par ordre
     */
    public function allOrdered(): array
    {
        return $this->orderBy('ordre', 'ASC')->findAll();
    }

    /**
     * Trouve la catégorie IMC correspondant à une valeur IMC donnée
     */
    public function findByIMC(float $imc): ?array
    {
        $category = $this->where('imc_min <=', $imc)
            ->where('imc_max >=', $imc)
            ->first();

        return is_array($category) ? $category : null;
    }

    /**
     * Obtient une catégorie par son ID
     */
    public function getById(int $id): ?array
    {
        $category = $this->find($id);
        return is_array($category) ? $category : null;
    }
}
