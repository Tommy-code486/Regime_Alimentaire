<?php

namespace App\Controllers;

use App\Models\SportModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class SportController extends BaseController
{
    protected $sportModel;

    public function __construct()
    {
        helper(['form', 'url']);
        $this->sportModel = new SportModel();
    }

    public function index(): string
    {
        $sports = $this->sportModel->getAllSports();

        return view('sports/index', [
            'sports' => $sports,
            'title' => 'Activites sportives',
            'action' => site_url('sports/store'),
            'sport' => [
                'id' => $this->sportModel->getNextId(),
                'nom' => '',
                'description' => '',
                'calories_par_heure' => '',
                'actif' => 1,
            ],
            'errors' => session()->getFlashdata('errors') ?? [],
            'isEdit' => false,
        ]);
    }

    public function create(): string
    {
        return $this->index();
    }

    public function store()
    {
        $result = $this->sportModel->createSport($this->request->getPost());

        if (!$result['success']) {
            return redirect()->back()->withInput()->with('errors', $result['errors']);
        }

        return redirect()->to(site_url('sports'))
            ->with('message', 'Activite ajoutee.')
            ->with('message_type', 'success');
    }

    public function edit(int $id): string
    {
        $sport = $this->sportModel->getSportById($id);

        if (!$sport) {
            throw PageNotFoundException::forPageNotFound();
        }

        $sports = $this->sportModel->getAllSports();

        return view('sports/index', [
            'sports' => $sports,
            'title' => 'Activites sportives',
            'action' => site_url('sports/update/' . $id),
            'sport' => $sport,
            'errors' => session()->getFlashdata('errors') ?? [],
            'isEdit' => true,
        ]);
    }

    public function update(int $id)
    {
        $result = $this->sportModel->updateSport($id, $this->request->getPost());

        if (!$result['success']) {
            return redirect()->back()->withInput()->with('errors', $result['errors']);
        }

        return redirect()->to(site_url('sports'))
            ->with('message', 'Activite mise a jour.')
            ->with('message_type', 'success');
    }

    public function delete(int $id)
    {
        $result = $this->sportModel->deleteSport($id);

        if (!$result['success'] && !empty($result['notFound'])) {
            throw PageNotFoundException::forPageNotFound();
        }

        if (!$result['success']) {
            return redirect()->to(site_url('sports'))
                ->with('message', 'Suppression impossible.')
                ->with('message_type', 'error');
        }

        return redirect()->to(site_url('sports'))
            ->with('message', 'Activite supprimee.')
            ->with('message_type', 'success');
    }
}
