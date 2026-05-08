<?php

namespace App\Controllers;

use App\Models\Regime_model;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Database;

class Profil extends BaseController
{
    protected $db;
    protected $regimeModel;

    public function initController($request, $response, $logger)
    {
        parent::initController($request, $response, $logger);

        helper('url');
        $this->db = Database::connect();
        $this->regimeModel = new Regime_model();
    }

    public function index(): string
    {
        // On récupère l'utilisateur connecté (ID 1 pour le test)
        $user = $this->db->table('users')->where('id', 1)->get()->getRow();

        if (!$user) {
            return view('regimeChoix', [
                'user' => (object) ['prenom' => 'Utilisateur', 'poids' => 0, 'taille' => 0],
                'imc' => 0,
            ]);
        }

        // Calcul de l'IMC : Poids / (Taille en m)²
        $taille_m = max(0.01, $user->taille / 100);
        $imc = $user->poids / ($taille_m * $taille_m);

        return view('regimeChoix', [
            'user' => $user,
            'imc' => round($imc, 1),
        ]);
    }

    public function get_regimes_json(string $objectif): ResponseInterface
    {
        $resultat = $this->regimeModel->get_regimes_complets($objectif);

        return $this->response->setJSON($resultat);
    }
}