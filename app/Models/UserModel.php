<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useAutoIncrement = true;
    protected $allowedFields = ['nom','prenom','email','mot_de_passe','genre','taille',
                                'poids','imc','solde_portefeuille','option_gold','created_at',];

    public function findByEmail(string $email): ?array
    {
        $user = $this->where('email', $email)->first();

        return $user === null ? null : $user;
    }

    public function createAccount(array $data): int
    {
        $this->insert($data);

        return (int) $this->getInsertID();
    }
}