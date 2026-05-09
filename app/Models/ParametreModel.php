<?php

namespace App\Models;

use CodeIgniter\Model;

class ParametreModel extends Model
{
    protected $table = 'parametres';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useAutoIncrement = false;
    protected $allowedFields = ['id', 'cle', 'valeur', 'description', 'created_at'];

    public function getValue(string $key, ?string $default = null): ?string
    {
        $row = $this->where('cle', trim($key))->first();

        if (! is_array($row)) {
            return $default;
        }

        return isset($row['valeur']) ? (string) $row['valeur'] : $default;
    }

    public function getFloat(string $key, float $default): float
    {
        $value = $this->getValue($key);

        return is_numeric($value) ? (float) $value : $default;
    }
}
