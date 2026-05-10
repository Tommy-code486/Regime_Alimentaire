<?php

namespace App\Controllers;

use App\Models\IMCCategoryModel;
use App\Models\ObjectifModel;
use App\Models\ParametreModel;
use App\Models\RegimeModel;
use App\Models\SouscriptionModel;
use App\Models\UserModel;
use App\Models\ActiviteObjectifModel;

class Dashboard extends BaseController
{
    public function index()
    {
        if (! session()->get('isLoggedIn')) {
            return redirect()->to(site_url('login'));
        }

        if (session('accountType') === 'admin') {
            return redirect()->to(site_url('admin/dashboard'));
        }

        $userModel = new UserModel();
        $objectifModel = new ObjectifModel();
        $souscriptionModel = new SouscriptionModel();
        $imcCategoryModel = new IMCCategoryModel();

        $user = $this->loadCurrentUser($userModel);
        $objectifs = $objectifModel->allOrdered();
        $selectedObjectif = $this->resolveSelectedObjectif($objectifs, (float) ($user['imc'] ?? 0));
        $activeSouscription = $souscriptionModel->findActiveByUser((int) session('userId'));
        $subscriptionHistory = $souscriptionModel->findHistoryByUser((int) session('userId'));
        $historyFilter = strtolower(trim((string) $this->request->getGet('history')));
        if (! in_array($historyFilter, ['all', 'active', 'ended'], true)) {
            $historyFilter = 'all';
        }

        if ($historyFilter !== 'all') {
            $today = strtotime(date('Y-m-d'));
            $subscriptionHistory = array_values(array_filter($subscriptionHistory, static function (array $item) use ($historyFilter, $today): bool {
                $isActive = ! empty($item['date_fin']) && strtotime((string) $item['date_fin']) >= $today;

                return $historyFilter === 'active' ? $isActive : ! $isActive;
            }));
        }
        $imcCategories = $imcCategoryModel->allOrdered();

        $currentWeight = (float) ($user['poids'] ?? 0);
        $targetWeight = $this->targetWeightByObjectif($selectedObjectif['nom'], $currentWeight);
        $userIMCCategory = $imcCategoryModel->findByIMC((float) ($user['imc'] ?? 0));

        return view('dashboard_user', $this->baseViewData([
            'pageTitle' => 'Tableau de bord utilisateur',
            'pageHeading' => 'Bonjour, ' . $this->displayName() ,
            'pageSubtitle' => 'Suivi de votre profil nutritionnel',
            'activeMenu' => 'dashboard',
            'accountBadge' => session('option_gold') ? 'Gold' : 'Standard',
            'objectifs' => $objectifs,
            'selectedObjectif' => $selectedObjectif['nom'],
            'selectedObjectifLabel' => $selectedObjectif['label'],
            'poidsActuel' => $currentWeight,
            'poidsObjectif' => $targetWeight,
            'regimeActifNom' => is_array($activeSouscription) ? (string) ($activeSouscription['regime_nom'] ?? 'Aucun') : 'Aucun',
            'regimeActifSemaine' => is_array($activeSouscription) ? $this->computeCurrentWeek((string) ($activeSouscription['date_debut'] ?? '')) : null,
            'regimeActifDuree' => is_array($activeSouscription) ? (int) ($activeSouscription['regime_duree'] ?? 0) : 0,
            'subscriptionHistory' => $subscriptionHistory,
            'historyFilter' => $historyFilter,
            'imcCategories' => $imcCategories,
            'userIMCCategory' => $userIMCCategory,
        ]));
    }

    public function updateObjectif()
    {
        if (! session()->get('isLoggedIn')) {
            return redirect()->to(site_url('login'));
        }

        if (session('accountType') !== 'user') {
            return redirect()->to(site_url('admin/dashboard'));
        }

        $objectifNom = trim((string) $this->request->getPost('objectif'));
        if ($objectifNom === '') {
            return redirect()->back()->with('authError', 'Veuillez choisir un objectif.');
        }

        $objectifModel = new ObjectifModel();
        $objectif = $objectifModel->findByNom($objectifNom);
        if (! is_array($objectif)) {
            return redirect()->back()->with('authError', 'Objectif invalide.');
        }

        session()->set('objectif_choisi', (string) ($objectif['nom'] ?? ''));

        return redirect()->to(site_url('dashboard'))->with('authSuccess', 'Objectif mis à jour.');
    }

    /**
     * Met à jour la catégorie IMC cible et réinitialise l'objectif à 'equilibre'
     */
    public function updateIMCTarget()
    {
        if (! session()->get('isLoggedIn')) {
            return redirect()->to(site_url('login'));
        }

        if (session('accountType') !== 'user') {
            return redirect()->to(site_url('admin/dashboard'));
        }

        $categoryId = (int) $this->request->getPost('imc_category_id');
        if ($categoryId <= 0) {
            return redirect()->back()->with('authError', 'Catégorie IMC invalide.');
        }

        $imcCategoryModel = new IMCCategoryModel();
        $category = $imcCategoryModel->getById($categoryId);
        if (! is_array($category)) {
            return redirect()->back()->with('authError', 'Catégorie IMC non trouvée.');
        }

        // Calculer l'IMC cible (moyenne de la catégorie)
        $targetIMC = (float) (($category['imc_min'] + $category['imc_max']) / 2);

        // Stocker en session
        session()->set('objectif_choisi', 'equilibre');
        session()->set('imc_target', $targetIMC);
        session()->set('imc_target_category_id', $categoryId);
        session()->set('imc_target_category_name', $category['nom']);

        return redirect()->to(site_url('dashboard'))->with('authSuccess', 'Catégorie IMC cible sélectionnée.');
    }

    public function admin()
    {
        if (! session()->get('isLoggedIn')) {
            return redirect()->to(site_url('login'));
        }

        if (session('accountType') !== 'admin') {
            return redirect()->to(site_url('dashboard'));
        }

        return view('dashboard_admin', $this->baseViewData([
            'pageTitle' => 'Tableau de bord admin',
            'pageHeading' => 'Bonjour, ' . $this->displayName(),
            'pageSubtitle' => 'Pilotage et supervision de la plateforme',
            'activeMenu' => 'dashboard',
            'accountBadge' => strtoupper((string) session('roleLabel')),
        ]));
    }

    public function gold()
    {
        if (! session()->get('isLoggedIn')) {
            return redirect()->to(site_url('login'));
        }

        if (session('accountType') !== 'user') {
            return redirect()->to(site_url('admin/dashboard'));
        }

        $userModel = new UserModel();
        $parametreModel = new ParametreModel();
        $user = $this->loadCurrentUser($userModel);

        $goldPrice = $parametreModel->getFloat('prix_option_gold', 25000);
        $wallet = (float) ($user['solde_portefeuille'] ?? 0);
        $missing = max(0, $goldPrice - $wallet);

        return view('option_gold', $this->baseViewData([
            'pageTitle' => 'Option Gold',
            'pageHeading' => 'Option Gold',
            'pageSubtitle' => 'Paiement unique et remise sur les régimes',
            'activeMenu' => 'gold',
            'accountBadge' => session('option_gold') ? 'Gold actif' : 'À activer',
            'goldPrice' => $goldPrice,
            'goldMissing' => $missing,
        ]));
    }

    public function regimes()
    {
        if (! session()->get('isLoggedIn')) {
            return redirect()->to(site_url('login'));
        }

        if (session('accountType') !== 'user') {
            return redirect()->to(site_url('admin/dashboard'));
        }

        $userModel = new UserModel();
        $objectifModel = new ObjectifModel();
        $regimeModel = new RegimeModel();
        $parametreModel = new ParametreModel();
        $imcCategoryModel = new IMCCategoryModel();
        $activiteObjectifModel = new ActiviteObjectifModel();

        $user = $this->loadCurrentUser($userModel);
        $userIMC = (float) ($user['imc'] ?? 0);
        $objectifs = $objectifModel->allOrdered();
        $selected = $this->resolveSelectedObjectif($objectifs, $userIMC);

        // Vérifier si l'utilisateur a une catégorie IMC cible sélectionnée
        $targetIMC = session('imc_target');
        $targetCategoryName = session('imc_target_category_name');

        if ($targetIMC !== null && (float) $targetIMC > 0) {
            // Utiliser la logique de comparaison IMC
            $regimes = $regimeModel->getSuggestedByIMCComparison($userIMC, (float) $targetIMC);
            $selectedLabel = 'IMC idéal : ' . $targetCategoryName;
            $sports = $activiteObjectifModel->getActivitesByIMCComparison($userIMC, (float) $targetIMC, 5);
        } else {
            // Logique traditionnelle par filtre
            $filter = trim((string) $this->request->getGet('objectif'));
            if ($filter !== '') {
                $isKnown = false;
                foreach ($objectifs as $objectif) {
                    if ((string) ($objectif['nom'] ?? '') === $filter) {
                        $isKnown = true;
                        break;
                    }
                }

                if ($isKnown) {
                    $selected['nom'] = $filter;
                    session()->set('objectif_choisi', $filter);
                }
            }

            $regimes = $regimeModel->getSuggestedByObjectif($selected['nom']);
            $selectedLabel = $selected['label'];

            // Obtenir les activités sportives pour l'objectif sélectionné
            $objectifId = $this->getObjectifIdByNom($objectifs, $selected['nom']);
            $sports = $objectifId > 0 ? $activiteObjectifModel->getActivitesByObjectif($objectifId, 5) : [];
        }

        $remiseGold = $parametreModel->getFloat('remise_gold', 15);

        foreach ($regimes as &$regime) {
            $regimePrices = $regime['prices'] ?? [];
            $bestPrice = null;
            foreach ($regimePrices as $priceRow) {
                $amount = (float) ($priceRow['prix'] ?? 0);
                if ($amount <= 0) {
                    continue;
                }
                if ($bestPrice === null || $amount < $bestPrice) {
                    $bestPrice = $amount;
                }
            }

            $regime['base_price'] = $bestPrice;
            $regime['gold_price'] = $bestPrice !== null ? $bestPrice * (1 - ($remiseGold / 100)) : null;
        }

        return view('regimes_sugges', $this->baseViewData([
            'pageTitle' => 'Régimes suggérés',
            'pageHeading' => 'Régimes recommandés',
            'pageSubtitle' => 'Suggestions adaptées à votre profil',
            'activeMenu' => 'regimes',
            'accountBadge' => session('option_gold') ? 'Gold' : 'Standard',
            'objectifs' => $objectifs,
            'selectedObjectif' => $selected['nom'],
            'selectedObjectifLabel' => $selectedLabel,
            'regimes' => $regimes,
            'remiseGold' => $remiseGold,
            'sports' => $sports,
            'userIMC' => $userIMC,
            'targetIMC' => $targetIMC,
        ]));
    }
    


    private function baseViewData(array $data): array
    {
        return array_merge([
            'displayName' => $this->displayName(),
            'displayEmail' => (string) session('email'),
            'roleLabel' => (string) session('roleLabel'),
            'accountType' => (string) session('accountType'),
            'isGold' => (bool) session('option_gold'),
            'imc' => session('imc'),
            'solde_portefeuille' => session('solde_portefeuille'),
        ], $data);
    }

    private function loadCurrentUser(UserModel $userModel): array
    {
        $user = $userModel->find((int) session('userId'));

        if (! is_array($user)) {
            return [
                'imc' => session('imc'),
                'solde_portefeuille' => session('solde_portefeuille'),
                'option_gold' => session('option_gold'),
                'poids' => 0,
            ];
        }

        session()->set([
            'imc' => $user['imc'] ?? null,
            'solde_portefeuille' => $user['solde_portefeuille'] ?? null,
            'option_gold' => (int) ($user['option_gold'] ?? 0),
        ]);

        return $user;
    }

    private function resolveSelectedObjectif(array $objectifs, float $imc): array
    {
        $selectedNom = trim((string) session('objectif_choisi'));
        if ($selectedNom !== '') {
            foreach ($objectifs as $objectif) {
                if ((string) ($objectif['nom'] ?? '') === $selectedNom) {
                    return [
                        'nom' => $selectedNom,
                        'label' => $this->objectifLabel($selectedNom),
                    ];
                }
            }
        }

        if ($imc > 25) {
            $selectedNom = 'reduction';
        } elseif ($imc < 18.5) {
            $selectedNom = 'augmentation';
        } else {
            $selectedNom = 'equilibre';
        }

        session()->set('objectif_choisi', $selectedNom);

        return [
            'nom' => $selectedNom,
            'label' => $this->objectifLabel($selectedNom),
        ];
    }

    private function objectifLabel(string $objectifNom): string
    {
        if ($objectifNom === 'reduction') {
            return 'Réduire le poids';
        }

        if ($objectifNom === 'augmentation') {
            return 'Augmenter le poids';
        }

        return 'IMC idéal';
    }

    private function targetWeightByObjectif(string $objectifNom, float $currentWeight): float
    {
        if ($currentWeight <= 0) {
            return 0;
        }

        if ($objectifNom === 'reduction') {
            return max(1, round($currentWeight - 5, 1));
        }

        if ($objectifNom === 'augmentation') {
            return round($currentWeight + 5, 1);
        }

        return round($currentWeight, 1);
    }

    private function computeCurrentWeek(string $startDate): ?int
    {
        if ($startDate === '') {
            return null;
        }

        $timestamp = strtotime($startDate);
        if ($timestamp === false) {
            return null;
        }

        $elapsedDays = (int) floor((time() - $timestamp) / 86400);

        return max(1, (int) floor($elapsedDays / 7) + 1);
    }

    private function displayName(): string
    {
        $displayName = trim((string) session('displayName'));

        return $displayName !== '' ? $displayName : 'Utilisateur';
    }

    /**
     * Obtient l'ID de l'objectif par son nom
     */
    private function getObjectifIdByNom(array $objectifs, string $objectifNom): int
    {
        foreach ($objectifs as $objectif) {
            if ((string) ($objectif['nom'] ?? '') === $objectifNom) {
                return (int) ($objectif['id'] ?? 0);
            }
        }

        return 0;
    }
}