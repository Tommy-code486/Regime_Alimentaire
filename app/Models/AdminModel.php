<?php

namespace App\Models;

use CodeIgniter\Model;

class AdminModel extends Model
{
    protected $table = 'admins';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useAutoIncrement = false;
    protected $allowedFields = [
        'id',
        'nom',
        'email',
        'mot_de_passe',
        'role',
    ];

    public function findByEmail(string $email): ?array
    {
        $admin = $this->where('email', $email)->first();

        return $admin === null ? null : $admin;
    }
}