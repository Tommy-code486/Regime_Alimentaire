<?php
namespace App\Models;

use CodeIgniter\Model;
use Config\Services;

class PortfModel extends Model
{
    protected $table = 'codes_portefeuille';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $code = 'code';
    protected $montant = 'montant';
    protected $est_valide = 'est_valide';
    protected $created_at = 'created_at';
    protected $returnType = 'array';
    protected $allowedFields = [
        'id',
        'code',
        'montant',
        'est_valide',
        'created_at',
    ];

    public function getCode(string $code): ?array
    {
        $code = trim($code);
        if ($code === '') {
            return null;
        }

        $row = $this->where('code', $code)->first();

        return $row ?: null;
    }

    public function validationCode(string $code): array
    {
        $row = $this->getCode($code);

        if ($row === null) {
            return [
                'success' => false,
                'message' => 'Code invalide.',
            ];
        }

        if ((int) ($row['est_valide'] ?? 0) === 1) {
            return [
                'success' => false,
                'message' => 'Code deja utilise.',
            ];
        }

        return [
            'success' => true,
            'code' => $row,
        ];
    }
    
}