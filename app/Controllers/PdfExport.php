<?php

namespace App\Controllers;

use App\Models\ActiviteObjectifModel;
use App\Models\ObjectifModel;
use App\Models\RegimeModel;
use App\Models\SouscriptionModel;
use App\Models\UserModel;

class PdfExport extends BaseController
{
    private function pdfText(string $text): string
    {
        if ($text === '') {
            return '';
        }

        $converted = iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $text);

        return $converted !== false ? $converted : $text;
    }
    public function regimes(): 
        \CodeIgniter\HTTP\ResponseInterface
    {
        if (! session()->get('isLoggedIn')) {
            return redirect()->to(site_url('login'));
        }

        if (! defined('FPDF_FONTPATH')) {
            define('FPDF_FONTPATH', APPPATH . 'Models/font/');
        }

        require_once APPPATH . 'Controllers/fpdf.php';

        $regimeModel = new RegimeModel();
        $regimes = $regimeModel->getRegimesWithDetails();

        $pdf = new \FPDF('P', 'mm', 'A4');
        $pdf->SetTitle('Liste des regimes');
        $pdf->SetAuthor('Regime Alimentaire');
        $pdf->AddPage();
    $pdf->SetFont('Helvetica', 'B', 16);
    $pdf->Cell(0, 10, $this->pdfText('Liste des régimes'), 0, 1, 'C');
        $pdf->Ln(2);

    $pdf->SetFont('Helvetica', 'B', 11);
        $pdf->SetFillColor(230, 230, 230);
    $pdf->Cell(55, 8, $this->pdfText('Régime'), 1, 0, 'L', true);
    $pdf->Cell(30, 8, $this->pdfText('Objectif'), 1, 0, 'L', true);
    $pdf->Cell(25, 8, $this->pdfText('Durée'), 1, 0, 'C', true);
    $pdf->Cell(40, 8, $this->pdfText('Variation'), 1, 0, 'C', true);
    $pdf->Cell(35, 8, $this->pdfText('Prix min'), 1, 1, 'C', true);

    $pdf->SetFont('Helvetica', '', 10);
        foreach ($regimes as $regime) {
            $prices = $regime['prices'] ?? [];
            $minPrice = null;
            foreach ($prices as $price) {
                $amount = (float) ($price['prix'] ?? 0);
                if ($amount <= 0) {
                    continue;
                }
                if ($minPrice === null || $amount < $minPrice) {
                    $minPrice = $amount;
                }
            }

            $pdf->Cell(55, 8, $this->pdfText((string) ($regime['nom'] ?? '')), 1);
            $pdf->Cell(30, 8, $this->pdfText((string) ($regime['objectif_nom'] ?? '')), 1);
            $pdf->Cell(25, 8, (string) ($regime['duree_semaines'] ?? '') . ' sem', 1, 0, 'C');
            $variation = (float) ($regime['variation_poids'] ?? 0);
            $pdf->Cell(40, 8, number_format($variation, 1, ',', ' ') . ' kg', 1, 0, 'C');
            $priceLabel = $minPrice !== null ? number_format($minPrice, 0, ',', ' ') . ' Ar' : 'N/A';
            $pdf->Cell(35, 8, $priceLabel, 1, 1, 'C');
        }

        $output = $pdf->Output('S');

        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'attachment; filename="regimes.pdf"')
            ->setBody($output);
    }

    public function regimeFiche(): \CodeIgniter\HTTP\ResponseInterface
    {
        if (! session()->get('isLoggedIn')) {
            return redirect()->to(site_url('login'));
        }

        if (session('accountType') !== 'user') {
            return redirect()->to(site_url('dashboard'));
        }

        if (! defined('FPDF_FONTPATH')) {
            define('FPDF_FONTPATH', APPPATH . 'Models/font/');
        }

        require_once APPPATH . 'Controllers/fpdf.php';

        $userId = (int) session('userId');
        $userModel = new UserModel();
        $souscriptionModel = new SouscriptionModel();
        $objectifModel = new ObjectifModel();
        $activiteObjectifModel = new ActiviteObjectifModel();

        $user = $userModel->find($userId) ?? [];
        $userIMC = (float) ($user['imc'] ?? 0);

        $activeSouscription = $souscriptionModel->findActiveByUser($userId);
        if (! is_array($activeSouscription)) {
            return redirect()->to(site_url('dashboard'))
                ->with('authError', 'Veuillez choisir un regime avant de telecharger la fiche PDF.');
        }

        $objectifNom = trim((string) ($activeSouscription['objectif_choisi'] ?? ''));
        if ($objectifNom === '') {
            $objectifNom = (string) ($activeSouscription['objectif_nom'] ?? 'equilibre');
        }

        $objectif = $objectifModel->findByNom($objectifNom);
        $objectifId = (int) ($objectif['id'] ?? 0);
        $objectifLabel = $objectif ? (string) ($objectif['nom'] ?? $objectifNom) : $objectifNom;

        $sports = $objectifId > 0 ? $activiteObjectifModel->getActivitesByObjectif($objectifId, 5) : [];

        $pdf = new \FPDF('P', 'mm', 'A4');
        $pdf->SetTitle('Fiche regime');
        $pdf->SetAuthor('Regime Alimentaire');
        $pdf->AddPage();

    $pdf->SetFont('Helvetica', 'B', 16);
    $pdf->Cell(0, 10, $this->pdfText('Fiche de régime personnalisée'), 0, 1, 'C');

    $pdf->SetFont('Helvetica', '', 11);
        $displayName = trim((string) ($user['prenom'] ?? '') . ' ' . (string) ($user['nom'] ?? ''));
        $displayName = $displayName !== '' ? $displayName : (string) session('displayName');
    $pdf->Cell(0, 7, $this->pdfText('Utilisateur: ') . $this->pdfText($displayName), 0, 1);
    $pdf->Cell(0, 7, $this->pdfText('Objectif: ') . $this->pdfText($objectifLabel), 0, 1);
        $pdf->Cell(0, 7, 'IMC actuel: ' . number_format($userIMC, 1, ',', ' '), 0, 1);

        $pdf->Ln(2);
    $pdf->SetFont('Helvetica', 'B', 12);
    $pdf->Cell(0, 8, $this->pdfText('Régime actif'), 0, 1);
    $pdf->SetFont('Helvetica', '', 10);

    $pdf->Cell(0, 7, $this->pdfText('Nom: ') . $this->pdfText((string) ($activeSouscription['regime_nom'] ?? '—')), 0, 1);
    $pdf->MultiCell(0, 6, $this->pdfText('Description: ') . $this->pdfText((string) ($activeSouscription['regime_description'] ?? '—')));
    $pdf->Cell(0, 7, $this->pdfText('Durée: ') . (string) ($activeSouscription['prix_duree'] ?? $activeSouscription['regime_duree'] ?? 0) . $this->pdfText(' semaines'), 0, 1);
        $pdf->Cell(0, 7, 'Variation: ' . number_format((float) ($activeSouscription['regime_variation_poids'] ?? 0), 1, ',', ' ') . ' kg', 0, 1);
    $pdf->Cell(0, 7, $this->pdfText('Composition: ') . (int) ($activeSouscription['regime_pourcentage_viande'] ?? 0) . $this->pdfText('% viande, ') . (int) ($activeSouscription['regime_pourcentage_poisson'] ?? 0) . $this->pdfText('% poisson, ') . (int) ($activeSouscription['regime_pourcentage_volaille'] ?? 0) . $this->pdfText('% volaille'), 0, 1);
        $pdf->Cell(0, 7, 'Prix: ' . number_format((float) ($activeSouscription['montant_paye'] ?? $activeSouscription['prix_regime'] ?? 0), 0, ',', ' ') . ' Ar', 0, 1);
    $pdf->Cell(0, 7, $this->pdfText('Période: ') . (string) ($activeSouscription['date_debut'] ?? '') . ' -> ' . (string) ($activeSouscription['date_fin'] ?? ''), 0, 1);

        if (! empty($sports)) {
            $pdf->Ln(3);
            $pdf->SetFont('Helvetica', 'B', 11);
            $pdf->Cell(0, 8, $this->pdfText('Activités sportives conseillées'), 0, 1);
            $pdf->SetFont('Helvetica', '', 9);
            foreach ($sports as $sport) {
                $pdf->Cell(0, 6, $this->pdfText('- ' . (string) ($sport['nom'] ?? '') . ' (' . (string) ($sport['calories_par_heure'] ?? 0) . ' cal/h)'), 0, 1);
            }
        }

        $output = $pdf->Output('S');

        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'attachment; filename="fiche-regime.pdf"')
            ->setBody($output);
    }
}
