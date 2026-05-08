<?php

namespace App\Controllers;

class Auth extends BaseController
{
    public function login()
    {
        if (session()->get('isLoggedIn')) {
            return redirect()->to(session('accountType') === 'admin' ? site_url('admin/dashboard') : site_url('dashboard'));
        }

        return view('login_front', [
            'pageTitle' => 'Connexion',
        ]);
    }

    public function authenticate()
    {
        $rules = [
            'email' => 'required|valid_email',
            'mot_de_passe' => 'required|min_length[4]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('authError', implode(' ', $this->validator->getErrors()));
        }

        $email = strtolower(trim((string) $this->request->getPost('email')));
        $password = (string) $this->request->getPost('mot_de_passe');

        $userModel = model('UserModel');
        $adminModel = model('AdminModel');

        $user = $userModel->findByEmail($email);
        if ($user !== null && $this->passwordMatches($password, (string) $user['mot_de_passe'])) {
            $this->setUserSession($user, 'user', 'Utilisateur');

            return redirect()->to(site_url('dashboard'));
        }

        $admin = $adminModel->findByEmail($email);
        if ($admin !== null && $this->passwordMatches($password, (string) $admin['mot_de_passe'])) {
            $this->setAdminSession($admin);

            return redirect()->to(site_url('admin/dashboard'));
        }

        return redirect()->back()->withInput()->with('authError', 'Email ou mot de passe incorrect.');
    }

    public function registerStep1()
    {
        if (session()->get('isLoggedIn')) {
            return redirect()->to(session('accountType') === 'admin' ? site_url('admin/dashboard') : site_url('dashboard'));
        }

        return view('register_step1', [
            'pageTitle' => 'Inscription',
            'registrationStep1' => session('registration_step1') ?? [],
        ]);
    }

    public function storeStep1()
    {
        $rules = [
            'prenom' => 'required|min_length[2]|max_length[100]',
            'nom' => 'required|min_length[2]|max_length[100]',
            'email' => 'required|valid_email|max_length[100]',
            'mot_de_passe' => 'required|min_length[6]',
            'mot_de_passe_confirmation' => 'required|matches[mot_de_passe]',
            'genre' => 'required|in_list[M,F]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('authError', implode(' ', $this->validator->getErrors()));
        }

        $email = strtolower(trim((string) $this->request->getPost('email')));
        if ($this->emailExists($email)) {
            return redirect()->back()->withInput()->with('authError', 'Cet email est déjà utilisé.');
        }

        session()->set('registration_step1', [
            'prenom' => trim((string) $this->request->getPost('prenom')),
            'nom' => trim((string) $this->request->getPost('nom')),
            'email' => $email,
            'genre' => (string) $this->request->getPost('genre'),
            'mot_de_passe' => (string) $this->request->getPost('mot_de_passe'),
        ]);

        return redirect()->to(site_url('register/step2'));
    }

    public function registerStep2()
    {
        $step1 = session('registration_step1');
        if (! is_array($step1) || $step1 === []) {
            return redirect()->to(site_url('register'));
        }

        return view('register_step2', [
            'pageTitle' => 'Inscription - Santé',
            'registrationStep1' => $step1,
        ]);
    }

    public function storeStep2()
    {
        $step1 = session('registration_step1');
        if (! is_array($step1) || $step1 === []) {
            return redirect()->to(site_url('register'));
        }

        $rules = [
            'taille' => 'required|integer|greater_than[0]',
            'poids' => 'required|numeric|greater_than[0]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('authError', implode(' ', $this->validator->getErrors()));
        }

        $taille = (int) $this->request->getPost('taille');
        $poids = (float) $this->request->getPost('poids');
        $imc = round($poids / (($taille / 100) * ($taille / 100)), 1);

        $userModel = model('UserModel');
        $userId = $userModel->createAccount([
            'nom' => $step1['nom'],
            'prenom' => $step1['prenom'],
            'email' => $step1['email'],
            'mot_de_passe' => password_hash((string) $step1['mot_de_passe'], PASSWORD_DEFAULT),
            'genre' => $step1['genre'],
            'taille' => $taille,
            'poids' => $poids,
            'imc' => $imc,
            'solde_portefeuille' => 0,
            'option_gold' => 0,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        session()->remove('registration_step1');

        $user = $userModel->find($userId);
        if ($user === null) {
            return redirect()->to(site_url('login'))->with('authError', 'Inscription enregistrée, mais impossible de charger le compte.');
        }

        $this->setUserSession($user, 'user', 'Nouveau membre');

        return redirect()->to(site_url('dashboard'))->with('authSuccess', 'Compte créé avec succès.');
    }

    public function logout()
    {
        session()->destroy();

        return redirect()->to(site_url('login'));
    }

    private function passwordMatches(string $plainPassword, string $storedPassword): bool
    {
        return password_verify($plainPassword, $storedPassword) || hash_equals($storedPassword, $plainPassword);
    }

    private function emailExists(string $email): bool
    {
        $userModel = model('UserModel');
        $adminModel = model('AdminModel');

        return $userModel->findByEmail($email) !== null || $adminModel->findByEmail($email) !== null;
    }

    private function setUserSession(array $user, string $accountType, string $roleLabel): void
    {
        session()->set([
            'isLoggedIn' => true,
            'accountType' => $accountType,
            'roleLabel' => $roleLabel,
            'userId' => (int) $user['id'],
            'displayName' => trim((string) ($user['prenom'] ?? '') . ' ' . (string) ($user['nom'] ?? '')),
            'email' => (string) $user['email'],
            'imc' => $user['imc'] ?? null,
            'solde_portefeuille' => $user['solde_portefeuille'] ?? null,
            'option_gold' => (int) ($user['option_gold'] ?? 0),
        ]);
    }

    private function setAdminSession(array $admin): void
    {
        session()->set([
            'isLoggedIn' => true,
            'accountType' => 'admin',
            'roleLabel' => (string) ($admin['role'] ?? 'admin'),
            'userId' => (int) $admin['id'],
            'displayName' => (string) $admin['nom'],
            'email' => (string) $admin['email'],
        ]);
    }
}