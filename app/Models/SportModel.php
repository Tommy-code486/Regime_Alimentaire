<?php

namespace App\Models;

use Config\Services;
use CodeIgniter\Model;

class SportModel extends Model
{
	protected $table = 'activites_sportives';
	protected $primaryKey = 'id';
	protected $useAutoIncrement = false;
	protected $returnType = 'array';
	protected $allowedFields = [
		'id',
		'nom',
		'description',
		'calories_par_heure',
		'actif',
	];
	protected $useTimestamps = false;

	public function getAllSports(): array
	{
		return $this->orderBy('id', 'ASC')->findAll();
	}

	public function getSportById(int $id): ?array
	{
		$sport = $this->find($id);

		return $sport ?: null;
	}

	public function getNextId(): int
	{
		$row = $this->selectMax('id')->first();
		$maxId = $row['id'] ?? 0;

		return (int) $maxId + 1;
	}

	public function createSport(array $input): array
	{
		$validation = Services::validation();
		$validation->setRules([
			'id' => 'required|is_natural_no_zero|is_unique[activites_sportives.id]',
			'nom' => 'required|min_length[2]|max_length[100]',
			'description' => 'permit_empty|max_length[1000]',
			'calories_par_heure' => 'required|is_natural|greater_than_equal_to[50]|less_than_equal_to[1500]',
			'actif' => 'required|in_list[0,1]',
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

	public function updateSport(int $id, array $input): array
	{
		$validation = Services::validation();
		$validation->setRules([
			'nom' => 'required|min_length[2]|max_length[100]',
			'description' => 'permit_empty|max_length[1000]',
			'calories_par_heure' => 'required|is_natural|greater_than_equal_to[50]|less_than_equal_to[1500]',
			'actif' => 'required|in_list[0,1]',
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

	public function deleteSport(int $id): array
	{
		$sport = $this->find($id);

		if (! $sport) {
			return [
				'success' => false,
				'notFound' => true,
				'errors' => ['not_found' => 'Activite introuvable.'],
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

	private function buildPayload(array $input, bool $withId): array
	{
		$data = [
			'nom' => trim((string) ($input['nom'] ?? '')),
			'description' => (string) ($input['description'] ?? ''),
			'calories_par_heure' => (int) ($input['calories_par_heure'] ?? 0),
			'actif' => (int) ($input['actif'] ?? 0),
		];

		if ($withId) {
			$data['id'] = (int) ($input['id'] ?? 0);
		}

		return $data;
	}
}