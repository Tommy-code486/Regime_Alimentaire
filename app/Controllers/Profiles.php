<?php

namespace App\Controllers;

use App\Models\ProfileModel;

class Profiles extends BaseController
{
    public function index()
    {
        if (! session()->get('isLoggedIn')) {
            return redirect()->to(site_url('login'));
        }

        if (session('accountType') !== 'user') {
            return redirect()->to(site_url('admin/dashboard'));
        }

        $profileModel = new ProfileModel();
        $user = $profileModel->getProfileByUserId((int) session('userId'));

        if (! is_array($user)) {
            return redirect()->to(site_url('logout'));
        }

        return view('profiles/index', $this->profileViewData($user, [
            'pageTitle' => 'Mon profil',
            'pageHeading' => 'Complétion du profil',
            'pageSubtitle' => 'Vérifiez et complétez vos informations personnelles et de santé.',
            'activeMenu' => 'profile',
            'accountBadge' => 'Profil utilisateur',
            'formAction' => site_url('profiles/update'),
        ]));
    }

    public function update()
    {
        if (! session()->get('isLoggedIn')) {
            return redirect()->to(site_url('login'));
        }

        if (session('accountType') !== 'user') {
            return redirect()->to(site_url('admin/dashboard'));
        }

        $profileModel = new ProfileModel();
        $result = $profileModel->updateProfile((int) session('userId'), $this->request->getPost());

        if (! $result['success']) {
            return redirect()->back()->withInput()->with('authError', implode(' ', $result['errors'] ?? ['Impossible de mettre à jour le profil.']));
        }

        $updatedUser = $result['user'] ?? [];
        session()->set([
            'displayName' => trim((string) ($updatedUser['prenom'] ?? '') . ' ' . (string) ($updatedUser['nom'] ?? '')),
            'email' => (string) ($updatedUser['email'] ?? session('email')),
            'imc' => $updatedUser['imc'] ?? session('imc'),
        ]);

        return redirect()->to(site_url('profiles'))->with('authSuccess', 'Profil mis à jour avec succès.');
    }

    private function profileViewData(array $user, array $data = []): array
    {
        $profileModel = new ProfileModel();
        $completion = $profileModel->completionPercentage($user);
        $missingFields = $profileModel->missingFields($user);

        return array_merge($this->baseViewData([
            'userProfile' => $user,
            'profileCompletion' => $completion,
            'missingFields' => $missingFields,
            'profileCompletionClass' => $completion >= 80 ? 'good' : ($completion >= 50 ? 'warning' : 'danger'),
        ]), $data);
    }

    private function baseViewData(array $data): array
    {
        return array_merge([
            'displayName' => (string) session('displayName'),
            'displayEmail' => (string) session('email'),
            'roleLabel' => (string) session('roleLabel'),
            'accountType' => (string) session('accountType'),
            'isGold' => (bool) session('option_gold'),
            'imc' => session('imc'),
            'solde_portefeuille' => session('solde_portefeuille'),
        ], $data);
    }
}