<?php

namespace App\Controllers;

use App\Models\PrixRegimeModel;
use App\Models\RegimeModel;

class Regimes extends BaseController
{
    private RegimeModel $regimeModel;
    private PrixRegimeModel $prixRegimeModel;

    public function __construct()
    {
        $this->regimeModel = new RegimeModel();
        $this->prixRegimeModel = new PrixRegimeModel();
    }

    public function showRegimesList()
    {
        if (! session()->get('isLoggedIn')) {
            return redirect()->to(site_url('login'));
        }

        return view('liste_regime', $this->baseViewData([
            'pageTitle' => 'Régimes',
            'pageHeading' => 'Régimes et tarifs',
            'pageSubtitle' => 'Consultez, créez et gérez les régimes selon leurs objectifs et leurs durées.',
            'activeMenu' => 'regimes',
            'accountBadge' => session('accountType') === 'admin' ? 'Gestion admin' : 'Consultation',
            'regimes' => $this->regimeModel->getRegimesWithDetails(),
            'isAdmin' => session('accountType') === 'admin',
        ]));
    }

    public function create()
    {
        $this->assertAdmin();

        return view('regime_form', $this->baseViewData([
            'pageTitle' => 'Nouveau régime',
            'pageHeading' => 'Créer un régime',
            'pageSubtitle' => 'Définissez les pourcentages, la variation de poids et les tarifs par durée.',
            'activeMenu' => 'regimes',
            'accountBadge' => 'Création',
            'mode' => 'create',
            'regime' => [],
            'objectives' => $this->regimeModel->getObjectives(),
        ]));
    }

    public function store()
    {
        $this->assertAdmin();

        $regimeData = $this->validatedRegimeData();
        if ($regimeData === null) {
            return redirect()->back()->withInput()->with('authError', 'Veuillez vérifier les champs du régime et la somme des pourcentages.');
        }

        $regimeId = $this->regimeModel->saveRegime($regimeData);
        $this->prixRegimeModel->syncForRegime($regimeId, $this->collectPricesFromRequest());

        return redirect()->to(site_url('regimes-liste'))->with('authSuccess', 'Régime créé avec succès.');
    }

    public function edit(int $id)
    {
        $this->assertAdmin();

        $regime = $this->regimeModel->findWithDetails($id);
        if ($regime === null) {
            return redirect()->to(site_url('regimes-liste'))->with('authError', 'Régime introuvable.');
        }

        return view('regime_form', $this->baseViewData([
            'pageTitle' => 'Modifier le régime',
            'pageHeading' => 'Modifier un régime',
            'pageSubtitle' => 'Ajustez les paramètres du régime et les tarifs associés.',
            'activeMenu' => 'regimes',
            'accountBadge' => 'Edition',
            'mode' => 'edit',
            'regime' => $regime,
            'objectives' => $this->regimeModel->getObjectives(),
        ]));
    }

    public function update(int $id)
    {
        $this->assertAdmin();

        $regime = $this->regimeModel->find($id);
        if ($regime === null) {
            return redirect()->to(site_url('regimes-liste'))->with('authError', 'Régime introuvable.');
        }

        $regimeData = $this->validatedRegimeData();
        if ($regimeData === null) {
            return redirect()->back()->withInput()->with('authError', 'Veuillez vérifier les champs du régime et la somme des pourcentages.');
        }

        $this->regimeModel->updateRegime($id, $regimeData);
        $this->prixRegimeModel->syncForRegime($id, $this->collectPricesFromRequest());

        return redirect()->to(site_url('regimes-liste'))->with('authSuccess', 'Régime mis à jour avec succès.');
    }

    public function delete(int $id)
    {
        $this->assertAdmin();

        $regime = $this->regimeModel->find($id);
        if ($regime === null) {
            return redirect()->to(site_url('regimes-liste'))->with('authError', 'Régime introuvable.');
        }

        $this->prixRegimeModel->where('regime_id', $id)->delete();
        $this->regimeModel->deleteRegime($id);

        return redirect()->to(site_url('regimes-liste'))->with('authSuccess', 'Régime supprimé.');
    }

    private function baseViewData(array $data): array
    {
        return array_merge([
            'displayName' => trim((string) session('displayName')) ?: 'Utilisateur',
            'displayEmail' => (string) session('email'),
            'roleLabel' => (string) session('roleLabel'),
            'accountType' => (string) session('accountType'),
            'isGold' => (bool) session('option_gold'),
        ], $data);
    }

    private function assertAdmin(): void
    {
        if (session('accountType') !== 'admin') {
            redirect()->to(site_url('regimes-liste'))->send();
            exit;
        }
    }

    private function validatedRegimeData(): ?array
    {
        $rules = [
            'nom' => 'required|min_length[3]|max_length[100]',
            'description' => 'required|min_length[10]',
            'pourcentage_viande' => 'required|integer|greater_than_equal_to[0]|less_than_equal_to[100]',
            'pourcentage_poisson' => 'required|integer|greater_than_equal_to[0]|less_than_equal_to[100]',
            'pourcentage_volaille' => 'required|integer|greater_than_equal_to[0]|less_than_equal_to[100]',
            'variation_poids' => 'required|numeric',
            'duree_semaines' => 'required|integer|greater_than[0]',
            'id_objectif' => 'required|integer',
        ];

        if (! $this->validate($rules)) {
            return null;
        }

        $viande = (int) $this->request->getPost('pourcentage_viande');
        $poisson = (int) $this->request->getPost('pourcentage_poisson');
        $volaille = (int) $this->request->getPost('pourcentage_volaille');

        if (($viande + $poisson + $volaille) !== 100) {
            return null;
        }

        return [
            'nom' => trim((string) $this->request->getPost('nom')),
            'description' => trim((string) $this->request->getPost('description')),
            'pourcentage_viande' => $viande,
            'pourcentage_poisson' => $poisson,
            'pourcentage_volaille' => $volaille,
            'variation_poids' => (float) $this->request->getPost('variation_poids'),
            'duree_semaines' => (int) $this->request->getPost('duree_semaines'),
            'id_objectif' => (int) $this->request->getPost('id_objectif'),
            'actif' => $this->request->getPost('actif') ? 1 : 0,
        ];
    }

    private function collectPricesFromRequest(): array
    {
        $prices = [];

        for ($index = 1; $index <= 3; $index++) {
            $duration = (int) $this->request->getPost('prix_duree_' . $index);
            $price = (float) $this->request->getPost('prix_montant_' . $index);

            if ($duration > 0 && $price > 0) {
                $prices[] = [
                    'duree_semaines' => $duration,
                    'prix' => $price,
                ];
            }
        }

        return $prices;
    }
}