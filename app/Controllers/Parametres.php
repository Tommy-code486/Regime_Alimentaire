<?php

namespace App\Controllers;

use App\Models\ParametreModel;

class Parametres extends BaseController
{
    public function index()
    {
        $this->assertAdmin();

        $model = new ParametreModel();

        return view('parametres/index', [
            'pageTitle' => 'Paramètres',
            'pageHeading' => 'Paramètres applicatifs',
            'pageSubtitle' => 'Gestion des valeurs métier utilisées par l’application.',
            'activeMenu' => 'parametres',
            'accountBadge' => 'Administration',
            'parametres' => $model->getAllParametres(),
            'parametre' => [
                'id' => $model->getNextId(),
                'cle' => '',
                'valeur' => '',
                'description' => '',
            ],
            'action' => site_url('admin/parametres/store'),
            'isEdit' => false,
        ]);
    }

    public function create()
    {
        return $this->index();
    }

    public function store()
    {
        $this->assertAdmin();

        $model = new ParametreModel();
        $result = $model->createParametre($this->request->getPost());

        if (! $result['success']) {
            return redirect()->back()->withInput()->with('errors', $result['errors'] ?? []);
        }

        return redirect()->to(site_url('admin/parametres'))->with('authSuccess', 'Paramètre ajouté.');
    }

    public function edit(int $id)
    {
        $this->assertAdmin();

        $model = new ParametreModel();
        $parametre = $model->getParametreById($id);

        if (! is_array($parametre)) {
            return redirect()->to(site_url('admin/parametres'))->with('authError', 'Paramètre introuvable.');
        }

        return view('parametres/index', [
            'pageTitle' => 'Paramètres',
            'pageHeading' => 'Paramètres applicatifs',
            'pageSubtitle' => 'Gestion des valeurs métier utilisées par l’application.',
            'activeMenu' => 'parametres',
            'accountBadge' => 'Administration',
            'parametres' => $model->getAllParametres(),
            'parametre' => $parametre,
            'action' => site_url('admin/parametres/update/' . $id),
            'isEdit' => true,
        ]);
    }

    public function update(int $id)
    {
        $this->assertAdmin();

        $model = new ParametreModel();
        $result = $model->updateParametre($id, $this->request->getPost());

        if (! $result['success']) {
            if (! empty($result['notFound'])) {
                return redirect()->to(site_url('admin/parametres'))->with('authError', 'Paramètre introuvable.');
            }

            return redirect()->back()->withInput()->with('errors', $result['errors'] ?? []);
        }

        return redirect()->to(site_url('admin/parametres'))->with('authSuccess', 'Paramètre mis à jour.');
    }

    public function delete(int $id)
    {
        $this->assertAdmin();

        $model = new ParametreModel();
        $result = $model->deleteParametre($id);

        if (! $result['success']) {
            if (! empty($result['notFound'])) {
                return redirect()->to(site_url('admin/parametres'))->with('authError', 'Paramètre introuvable.');
            }

            return redirect()->to(site_url('admin/parametres'))->with('authError', 'Suppression impossible.');
        }

        return redirect()->to(site_url('admin/parametres'))->with('authSuccess', 'Paramètre supprimé.');
    }

    private function assertAdmin(): void
    {
        if (! session()->get('isLoggedIn')) {
            redirect()->to(site_url('login'))->send();
            exit;
        }

        if (session('accountType') !== 'admin') {
            redirect()->to(site_url('dashboard'))->send();
            exit;
        }
    }
}