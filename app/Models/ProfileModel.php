<?php

namespace App\Models;

use CodeIgniter\Model;
use Config\Services;

class ProfileModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useAutoIncrement = true;
    protected $allowedFields = ['nom', 'prenom', 'email', 'genre', 'taille', 'poids', 'imc'];

    public function getProfileByUserId(int $userId): ?array
    {
        $user = $this->find($userId);

        return $user === null ? null : $user;
    }

    public function updateProfile(int $userId, array $input): array
    {
        $validation = Services::validation();
        $validation->setRules([
            'prenom' => 'required|min_length[2]|max_length[100]',
            'nom' => 'required|min_length[2]|max_length[100]',
            'email' => 'required|valid_email|max_length[100]',
            'genre' => 'required|in_list[M,F]',
            'taille' => 'required|integer|greater_than[0]',
            'poids' => 'required|numeric|greater_than[0]',
        ]);

        if (! $validation->run($input)) {
            return [
                'success' => false,
                'errors' => $validation->getErrors(),
            ];
        }

        $data = [
            'prenom' => trim((string) $input['prenom']),
            'nom' => trim((string) $input['nom']),
            'email' => strtolower(trim((string) $input['email'])),
            'genre' => (string) $input['genre'],
            'taille' => (int) $input['taille'],
            'poids' => (float) $input['poids'],
        ];

        $data['imc'] = round($data['poids'] / (($data['taille'] / 100) * ($data['taille'] / 100)), 1);

        $existing = $this->where('email', $data['email'])->where('id !=', $userId)->first();
        if (is_array($existing)) {
            return [
                'success' => false,
                'errors' => ['email' => 'Cet email est déjà utilisé.'],
            ];
        }

        $updated = $this->update($userId, $data);

        if ($updated === false) {
            return [
                'success' => false,
                'errors' => $this->errors() ?: ['database' => 'Echec de la mise a jour du profil.'],
            ];
        }

        return [
            'success' => true,
            'user' => array_merge(['id' => $userId], $data),
        ];
    }

    public function completionPercentage(array $user): int
    {
        $fields = ['prenom', 'nom', 'email', 'genre', 'taille', 'poids', 'imc'];
        $filled = 0;

        foreach ($fields as $field) {
            $value = $user[$field] ?? null;
            if ($value !== null && $value !== '' && $value !== 0 && $value !== 0.0) {
                $filled++;
            }
        }

        return (int) round(($filled / count($fields)) * 100);
    }

    public function missingFields(array $user): array
    {
        $labels = [
            'prenom' => 'Prénom',
            'nom' => 'Nom',
            'email' => 'Email',
            'genre' => 'Genre',
            'taille' => 'Taille',
            'poids' => 'Poids',
            'imc' => 'IMC',
        ];

        $missing = [];

        foreach ($labels as $field => $label) {
            $value = $user[$field] ?? null;
            if ($value === null || $value === '' || $value === 0 || $value === 0.0) {
                $missing[] = $label;
            }
        }

        return $missing;
    }
}