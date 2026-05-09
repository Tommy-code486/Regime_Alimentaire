<?php

namespace App\Controllers;

use App\Models\ObjectifModel;
use App\Models\PaiementModel;
use App\Models\PrixRegimeModel;
use App\Models\RegimeModel;
use App\Models\SouscriptionModel;
use App\Models\UserModel;
use Config\Database;

class RegimeSubscription extends BaseController
{
    public function subscribe()
    {
        if (! session()->get('isLoggedIn')) {
            return redirect()->to(site_url('login'));
        }

        if (session('accountType') !== 'user') {
            return redirect()->to(site_url('admin/dashboard'));
        }

        $rules = [
            'regime_id' => 'required|integer',
            'prix_regime_id' => 'required|integer',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->with('authError', 'Sélection de régime invalide.');
        }

        $regimeId = (int) $this->request->getPost('regime_id');
        $prixRegimeId = (int) $this->request->getPost('prix_regime_id');
        $userId = (int) session('userId');

        $userModel = new UserModel();
        $regimeModel = new RegimeModel();
        $prixModel = new PrixRegimeModel();
        $souscriptionModel = new SouscriptionModel();
        $paiementModel = new PaiementModel();
        $objectifModel = new ObjectifModel();

        $user = $userModel->find($userId);
        if (! is_array($user)) {
            return redirect()->to(site_url('login'))->with('authError', 'Utilisateur introuvable.');
        }

        $regime = $regimeModel->findWithDetails($regimeId);
        if (! is_array($regime) || (int) ($regime['actif'] ?? 0) !== 1) {
            return redirect()->back()->with('authError', 'Régime indisponible.');
        }

        $selectedPrice = null;
        $prices = $prixModel->findByRegime($regimeId);
        foreach ($prices as $price) {
            if ((int) ($price['id'] ?? 0) === $prixRegimeId) {
                $selectedPrice = $price;
                break;
            }
        }

        if (! is_array($selectedPrice)) {
            return redirect()->back()->with('authError', 'Tarif de régime invalide.');
        }

        $baseAmount = (float) ($selectedPrice['prix'] ?? 0);
        $amountToPay = (int) ($user['option_gold'] ?? 0) === 1
            ? round($baseAmount * 0.85, 2)
            : $baseAmount;

        $wallet = (float) ($user['solde_portefeuille'] ?? 0);
        if ($wallet < $amountToPay) {
            return redirect()->back()->with('authError', 'Solde insuffisant pour souscrire ce régime.');
        }

        $objectifNom = trim((string) session('objectif_choisi'));
        if ($objectifNom === '') {
            $objectifNom = (string) ($regime['objectif_nom'] ?? 'equilibre');
        }

        $knownObjectif = $objectifModel->findByNom($objectifNom);
        if (! is_array($knownObjectif)) {
            $objectifNom = 'equilibre';
        }

        $durationWeeks = (int) ($selectedPrice['duree_semaines'] ?? 0);
        if ($durationWeeks <= 0) {
            $durationWeeks = (int) ($regime['duree_semaines'] ?? 4);
        }

        $startDate = date('Y-m-d');
        $endDate = date('Y-m-d', strtotime('+' . $durationWeeks . ' weeks'));

        $db = Database::connect();
        $db->transBegin();

        $newBalance = $wallet - $amountToPay;
        $updated = $userModel->update($userId, ['solde_portefeuille' => $newBalance]);
        if ($updated === false) {
            $db->transRollback();
            return redirect()->back()->with('authError', 'Impossible de débiter le portefeuille.');
        }

        $subscriptionId = $souscriptionModel->nextId();
        $subscriptionCreated = $souscriptionModel->insert([
            'id' => $subscriptionId,
            'user_id' => $userId,
            'regime_id' => $regimeId,
            'prix_regime_id' => $prixRegimeId,
            'objectif_choisi' => $objectifNom,
            'date_debut' => $startDate,
            'date_fin' => $endDate,
            'montant_paye' => $amountToPay,
        ]);

        if ($subscriptionCreated === false) {
            $db->transRollback();
            return redirect()->back()->with('authError', 'Impossible de créer la souscription.');
        }

        $paymentTypeId = $paiementModel->getTypeIdByName('regime') ?? 1;
        $paymentCreated = $paiementModel->insert([
            'id' => $paiementModel->nextId(),
            'user_id' => $userId,
            'souscription_id' => $subscriptionId,
            'montant' => $amountToPay,
            'id_type_paiement' => $paymentTypeId,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        if ($paymentCreated === false) {
            $db->transRollback();
            return redirect()->back()->with('authError', 'Impossible d\'enregistrer le paiement.');
        }

        $db->transCommit();

        session()->set('solde_portefeuille', $newBalance);

        return redirect()->to(site_url('dashboard'))->with('authSuccess', 'Souscription au régime effectuée avec succès.');
    }
}
