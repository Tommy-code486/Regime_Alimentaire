<?php

namespace App\Controllers;

use App\Models\SportModel;
use App\Models\ObjectifModel;
use App\Models\ActiviteObjectifModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class SportController extends BaseController
{
    private SportModel $sportModel;
    private ActiviteObjectifModel $activiteObjectifModel;
    private ObjectifModel $objectifModel;

    public function __construct()
    {
        $this->sportModel = new SportModel();
        $this->activiteObjectifModel = new ActiviteObjectifModel();
        $this->objectifModel = new ObjectifModel();
    }

    public function index(): string
    {
        $this->assertAdmin();

        $sports = $this->sportModel->getAllSports();
        $objectifs = $this->objectifModel->allOrdered();
        $priorities = [];

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
            'objectifs' => $objectifs,
            'priorities' => $priorities,
        ]);
        }

    public function create(): string
    {
        return $this->index();
    }

    public function store()
    {
        $this->assertAdmin();

        $result = $this->sportModel->createSport($this->request->getPost());

        if (! $result['success']) {
            return redirect()->back()->withInput()->with('errors', $result['errors']);
        }

        return redirect()->to(site_url('sports'))
            ->with('message', 'Activite ajoutee.')
            ->with('message_type', 'success');
    }

    public function edit(int $id): string
    {
        $this->assertAdmin();
        $sport = $this->sportModel->getSportById($id);

        if (! $sport) {
            throw PageNotFoundException::forPageNotFound();
        }

        $sports = $this->sportModel->getAllSports();
        $objectifs = $this->objectifModel->allOrdered();
        $priorities = $this->activiteObjectifModel->getPrioritiesForActivite($id);

        return view('sports/index', [
            'sports' => $sports,
            'title' => 'Activites sportives',
            'action' => site_url('sports/update/' . $id),
            'sport' => $sport,
            'errors' => session()->getFlashdata('errors') ?? [],
            'isEdit' => true,
            'objectifs' => $objectifs,
            'priorities' => $priorities,
        ]);
    }

    public function update(int $id)
    {
        $this->assertAdmin();
        
        $result = $this->sportModel->updateSport($id, $this->request->getPost());

        if (! $result['success']) {
            return redirect()->back()->withInput()->with('errors', $result['errors']);
        }

        return redirect()->to(site_url('sports'))
            ->with('message', 'Activite mise a jour.')
            ->with('message_type', 'success');
    }

    public function delete(int $id)
    {
        $this->assertAdmin();

        $result = $this->sportModel->deleteSport($id);

        if (! $result['success'] && ! empty($result['notFound'])) {
            throw PageNotFoundException::forPageNotFound();
        }

        if (! $result['success']) {
            return redirect()->to(site_url('sports'))
                ->with('message', 'Suppression impossible.')
                ->with('message_type', 'error');
        }

        return redirect()->to(site_url('sports'))
            ->with('message', 'Activite supprimee.')
            ->with('message_type', 'success');
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
