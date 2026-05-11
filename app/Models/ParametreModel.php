<?php

namespace App\Models;

use CodeIgniter\Model;

class ParametreModel extends Model
{
    protected $table = 'parametres';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useAutoIncrement = false;
    protected $allowedFields = ['id', 'cle', 'valeur', 'description', 'updated_at'];

    public function getAllParametres(): array
    {
        return $this->orderBy('id', 'ASC')->findAll();
    }

    public function getParametreById(int $id): ?array
    {
        $parametre = $this->find($id);

        return $parametre === null ? null : $parametre;
    }

    public function getNextId(): int
    {
        $row = $this->selectMax('id')->first();
        $maxId = $row['id'] ?? 0;

        return (int) $maxId + 1;
    }

    public function createParametre(array $input): array
    {
        $validation = service('validation');
        $validation->setRules([
            'id' => 'required|is_natural_no_zero|is_unique[parametres.id]',
            'cle' => 'required|min_length[2]|max_length[50]|is_unique[parametres.cle]',
            'valeur' => 'required|max_length[50]',
            'description' => 'permit_empty|max_length[1000]',
        ]);

        if (! $validation->run($input)) {
            return [
                'success' => false,
                'errors' => $validation->getErrors(),
            ];
        }

        $data = $this->buildPayload($input, true);
        $inserted = $this->insert($data);

        if ($inserted === false) {
            return [
                'success' => false,
                'errors' => $this->errors() ?: ['database' => 'Echec de la creation.'],
            ];
        }

        return [
            'success' => true,
            'id' => $data['id'],
        ];
    }

    public function updateParametre(int $id, array $input): array
    {
        $existing = $this->getParametreById($id);

        if (! is_array($existing)) {
            return [
                'success' => false,
                'notFound' => true,
                'errors' => ['not_found' => 'Parametre introuvable.'],
            ];
        }

        $validation = service('validation');
        $validation->setRules([
            'cle' => 'required|min_length[2]|max_length[50]|is_unique[parametres.cle,id,' . $id . ']',
            'valeur' => 'required|max_length[50]',
            'description' => 'permit_empty|max_length[1000]',
        ]);

        if (! $validation->run($input)) {
            return [
                'success' => false,
                'errors' => $validation->getErrors(),
            ];
        }

        $data = $this->buildPayload($input, false);
        $updated = $this->update($id, $data);

        if ($updated === false) {
            return [
                'success' => false,
                'errors' => $this->errors() ?: ['database' => 'Echec de la mise a jour.'],
            ];
        }

        return ['success' => true];
    }

    public function deleteParametre(int $id): array
    {
        $parametre = $this->getParametreById($id);

        if (! is_array($parametre)) {
            return [
                'success' => false,
                'notFound' => true,
                'errors' => ['not_found' => 'Parametre introuvable.'],
            ];
        }

        $deleted = $this->delete($id);

        if (! $deleted) {
            return [
                'success' => false,
                'errors' => $this->errors() ?: ['database' => 'Echec de la suppression.'],
            ];
        }

        return ['success' => true];
    }

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

    private function buildPayload(array $input, bool $withId): array
    {
        $data = [
            'cle' => trim((string) ($input['cle'] ?? '')),
            'valeur' => trim((string) ($input['valeur'] ?? '')),
            'description' => trim((string) ($input['description'] ?? '')),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        if ($withId) {
            $data['id'] = (int) ($input['id'] ?? 0);
        }

        return $data;
    }
}
