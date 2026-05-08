<?php

namespace App\Models;

use CodeIgniter\Model;

class StatsModel extends Model
{
    protected $db;

    public function __construct()
    {
        parent::__construct();
        $this->db = \Config\Database::connect();
    }

    public function usersByGender(): array
    {
        $builder = $this->db->table('users');
        $builder->select('genre, COUNT(*) as total');
        $builder->groupBy('genre');
        $res = $builder->get()->getResultArray();
        return $res;
    }

    public function registrationsOverMonths(int $months = 6): array
    {
        $builder = $this->db->table('users');
        $builder->select("DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as total");
        $builder->where('created_at >=', date('Y-m-d', strtotime("-{$months} months")));
        $builder->groupBy('month');
        $builder->orderBy('month', 'ASC');
        return $builder->get()->getResultArray();
    }

    public function revenueByMonth(int $months = 6): array
    {
        $builder = $this->db->table('paiements');
        $builder->select("DATE_FORMAT(created_at, '%Y-%m') as month, SUM(montant) as total");
        $builder->where('created_at >=', date('Y-m-d', strtotime("-{$months} months")));
        $builder->groupBy('month');
        $builder->orderBy('month', 'ASC');
        return $builder->get()->getResultArray();
    }

    public function revenueByObjective(): array
    {
        $builder = $this->db->table('souscriptions');
        $builder->select('objectif_choisi as objectif, SUM(montant_paye) as total');
        $builder->groupBy('objectif_choisi');
        $builder->orderBy('total', 'DESC');
        return $builder->get()->getResultArray();
    }

    public function subscriptionsByRegime(int $limit = 10): array
    {
        $builder = $this->db->table('souscriptions s');
        $builder->select('r.nom as regime, COUNT(s.id) as total');
        $builder->join('regimes r', 'r.id = s.regime_id', 'left');
        $builder->groupBy('r.nom');
        $builder->orderBy('total', 'DESC');
        $builder->limit($limit);
        return $builder->get()->getResultArray();
    }

    public function totals(): array
    {
        $res = [];
        $res['users'] = (int) $this->db->table('users')->countAllResults();
        $row = $this->db->table('paiements')->select('SUM(montant) as total')->get()->getRowArray();
        $res['revenue'] = isset($row['total']) ? (float) $row['total'] : 0.0;
        $res['subscriptions'] = (int) $this->db->table('souscriptions')->countAllResults();
        return $res;
    }
}
