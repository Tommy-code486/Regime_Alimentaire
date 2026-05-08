<?php

namespace App\Controllers;

use App\Models\StatsModel;

class AdminStats extends BaseController
{
    private StatsModel $statsModel;

    public function __construct()
    {
        $this->statsModel = new StatsModel();
    }

    public function index()
    {
        if (! session()->get('isLoggedIn') || session('accountType') !== 'admin') {
            return redirect()->to(site_url('login'));
        }

        $months = 6;
        $data = [
            'pageTitle' => 'Tableau de bord - Statistiques',
            'pageHeading' => 'Statistiques et rapports',
            'pageSubtitle' => 'Graphes et tableaux croisés basés sur la base de données',
            'activeMenu' => 'dashboard',
            'accountBadge' => strtoupper((string) session('roleLabel')),
            'totals' => $this->statsModel->totals(),
            'usersByGender' => $this->statsModel->usersByGender(),
            'registrations' => $this->statsModel->registrationsOverMonths($months),
            'revenueMonths' => $this->statsModel->revenueByMonth($months),
            'revenueByObjective' => $this->statsModel->revenueByObjective(),
            'topRegimes' => $this->statsModel->subscriptionsByRegime(10),
        ];

        // Prepare JSON for Chart.js
        $data['chart_users_gender'] = json_encode(array_column($data['usersByGender'], 'total'));
        $data['chart_users_gender_labels'] = json_encode(array_map(function ($r) { return $r['genre'] ?? 'Autre'; }, $data['usersByGender']));

        $data['chart_months_labels'] = json_encode(array_map(function ($r) { return $r['month']; }, $data['registrations']));
        $data['chart_registrations'] = json_encode(array_map(function ($r) { return (int) $r['total']; }, $data['registrations']));

        $data['chart_revenue_labels'] = json_encode(array_map(function ($r) { return $r['month']; }, $data['revenueMonths']));
        $data['chart_revenue'] = json_encode(array_map(function ($r) { return (float) $r['total']; }, $data['revenueMonths']));

        $data['chart_obj_labels'] = json_encode(array_map(function ($r) { return $r['objectif']; }, $data['revenueByObjective']));
        $data['chart_obj_values'] = json_encode(array_map(function ($r) { return (float) $r['total']; }, $data['revenueByObjective']));

        $data['top_regimes_labels'] = json_encode(array_map(function ($r) { return $r['regime']; }, $data['topRegimes']));
        $data['top_regimes_values'] = json_encode(array_map(function ($r) { return (int) $r['total']; }, $data['topRegimes']));

        return view('admin_stats', $this->baseViewData($data));
    }

    private function baseViewData(array $data): array
    {
        return array_merge([
            'displayName' => trim((string) session('displayName')) ?: 'Administrateur',
            'displayEmail' => (string) session('email'),
            'roleLabel' => (string) session('roleLabel'),
            'accountType' => (string) session('accountType'),
            'isGold' => (bool) session('option_gold'),
        ], $data);
    }
}
