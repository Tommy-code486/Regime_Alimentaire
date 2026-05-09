<?php

namespace App\Controllers;

use App\Models\PortfModel;
use App\Models\UserModel;

class Portefeuille extends BaseController
{
    public function validationCode()
    {
        if (! session()->get('isLoggedIn')) {
            return redirect()->to(site_url('login'));
        }

        if (session('accountType') !== 'user') {
            return redirect()->to(site_url('admin/dashboard'));
        }

        $rules = [
            'code' => 'required|max_length[50]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('authError', 'Veuillez saisir un code valide.');
        }

        $codeInput = strtoupper(trim((string) $this->request->getPost('code')));

        $portfModel = new PortfModel();
        $validation = $portfModel->validationCode($codeInput);

        if (! $validation['success']) {
            return redirect()->back()->withInput()->with('authError', (string) $validation['message']);
        }

        $codeRow = $validation['code'] ?? null;
        if (! is_array($codeRow)) {
            return redirect()->back()->withInput()->with('authError', 'Code invalide.');
        }

        $userId = (int) session('userId');
        $userModel = new UserModel();
        $user = $userModel->find($userId);

        if ($user === null) {
            return redirect()->back()->with('authError', 'Utilisateur introuvable.');
        }

        $montant = (float) ($codeRow['montant'] ?? 0);
        if ($montant <= 0) {
            return redirect()->back()->withInput()->with('authError', 'Code invalide.');
        }

        $db = $portfModel->db;
        $db->transBegin();

        $portfModel
            ->set('est_valide', 1)
            ->where('id', (int) $codeRow['id'])
            ->where('est_valide', 0)
            ->update();

        if ($db->affectedRows() !== 1) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('authError', 'Code deja utilise.');
        }

        $currentBalance = (float) ($user['solde_portefeuille'] ?? 0);
        $newBalance = $currentBalance + $montant;

        if ($userModel->update($userId, ['solde_portefeuille' => $newBalance]) === false) {
            $db->transRollback();
            return redirect()->back()->with('authError', 'Mise a jour du solde impossible.');
        }

        $db->transCommit();

        session()->set('solde_portefeuille', $newBalance);

        return redirect()->to(site_url('dashboard'))
            ->with('authSuccess', 'Porte-monnaie recharge de ' . number_format($montant, 0, ',', ' ') . ' Ar.');
    }
}
