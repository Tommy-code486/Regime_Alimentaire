<?php

namespace App\Controllers;

use App\Models\PaiementModel;
use App\Models\ParametreModel;
use App\Models\UserModel;
use Config\Database;

class Gold extends BaseController
{
    public function activate()
    {
        if (! session()->get('isLoggedIn')) {
            return redirect()->to(site_url('login'));
        }

        if (session('accountType') !== 'user') {
            return redirect()->to(site_url('admin/dashboard'));
        }

        $userId = (int) session('userId');
        $userModel = new UserModel();
        $parametreModel = new ParametreModel();
        $paiementModel = new PaiementModel();

        $user = $userModel->find($userId);
        if (! is_array($user)) {
            return redirect()->to(site_url('login'))->with('authError', 'Utilisateur introuvable.');
        }

        if ((int) ($user['option_gold'] ?? 0) === 1) {
            return redirect()->to(site_url('option-gold'))->with('authSuccess', 'Option Gold déjà active.');
        }

        $goldPrice = $parametreModel->getFloat('prix_option_gold', 25000);
        $wallet = (float) ($user['solde_portefeuille'] ?? 0);

        if ($wallet < $goldPrice) {
            return redirect()->to(site_url('option-gold'))->with('authError', 'Solde insuffisant pour activer Gold.');
        }

        $db = Database::connect();
        $db->transBegin();

        $newBalance = $wallet - $goldPrice;
        $userUpdated = $userModel->update($userId, [
            'option_gold' => 1,
            'solde_portefeuille' => $newBalance,
        ]);

        if ($userUpdated === false) {
            $db->transRollback();
            return redirect()->to(site_url('option-gold'))->with('authError', 'Activation Gold impossible.');
        }

        $paymentTypeId = $paiementModel->getTypeIdByName('gold') ?? 2;
        $paymentCreated = $paiementModel->insert([
            'id' => $paiementModel->nextId(),
            'user_id' => $userId,
            'souscription_id' => null,
            'montant' => $goldPrice,
            'id_type_paiement' => $paymentTypeId,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        if ($paymentCreated === false) {
            $db->transRollback();
            return redirect()->to(site_url('option-gold'))->with('authError', 'Paiement Gold non enregistré.');
        }

        $db->transCommit();

        session()->set([
            'option_gold' => 1,
            'solde_portefeuille' => $newBalance,
        ]);

        return redirect()->to(site_url('option-gold'))->with('authSuccess', 'Option Gold activée avec succès.');
    }
}
