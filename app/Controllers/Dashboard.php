<?php

namespace App\Controllers;

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

        return view('dashboard_user', $this->baseViewData([
            'pageTitle' => 'Tableau de bord utilisateur',
            'pageHeading' => 'Bonjour, ' . $this->displayName() . ' 👋',
            'pageSubtitle' => 'Suivi de votre profil nutritionnel',
            'activeMenu' => 'dashboard',
            'accountBadge' => session('option_gold') ? 'Gold' : 'Standard',
        ]));
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
            'pageHeading' => 'Bonjour, ' . $this->displayName() . ' 👋',
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

        return view('option_gold', $this->baseViewData([
            'pageTitle' => 'Option Gold',
            'pageHeading' => 'Option Gold',
            'pageSubtitle' => 'Paiement unique et remise sur les régimes',
            'activeMenu' => 'gold',
            'accountBadge' => session('option_gold') ? 'Gold actif' : 'À activer',
        ]));
    }

    public function regimes()
    {
        if (! session()->get('isLoggedIn')) {
            return redirect()->to(site_url('login'));
        }

        return view('regimes_sugges', $this->baseViewData([
            'pageTitle' => 'Régimes suggérés',
            'pageHeading' => 'Régimes recommandés 🥗',
            'pageSubtitle' => 'Suggestions adaptées à votre profil',
            'activeMenu' => 'regimes',
            'accountBadge' => session('option_gold') ? 'Gold' : 'Standard',
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
        ], $data);
    }

    private function displayName(): string
    {
        $displayName = trim((string) session('displayName'));

        return $displayName !== '' ? $displayName : 'Utilisateur';
    }
}