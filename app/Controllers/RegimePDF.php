<?php
namespace App\Controllers;

use App\Models\ActiviteObjectifModel;
use App\Models\ObjectifModel;
use App\Models\ParametreModel;
use App\Models\RegimeModel;
use App\Models\UserModel;

$vendorFpdf = ROOTPATH . 'vendor' . DIRECTORY_SEPARATOR . 'setasign' . DIRECTORY_SEPARATOR . 'fpdf' . DIRECTORY_SEPARATOR . 'fpdf.php';
if (is_file($vendorFpdf)) {
    require_once $vendorFpdf;
} else {
    if (! defined('FPDF_FONTPATH')) {
        define('FPDF_FONTPATH', APPPATH . 'Controllers' . DIRECTORY_SEPARATOR . 'font' . DIRECTORY_SEPARATOR);
    }
    require_once APPPATH . 'Controllers' . DIRECTORY_SEPARATOR . 'fpdf.php';
}

class RegimePDF extends BaseController
{
    public function exportPDF()
    {
        if (! session()->get('isLoggedIn')) {
            return redirect()->to(site_url('login'));
        }

        if (session('accountType') !== 'user') {
            return redirect()->to(site_url('admin/dashboard'));
        }

        $userModel = new UserModel();
        $objectifModel = new ObjectifModel();
        $regimeModel = new RegimeModel();
        $parametreModel = new ParametreModel();
        $activiteObjectifModel = new ActiviteObjectifModel();

        $user = $userModel->find((int) session('userId'));
        $userIMC = (float) (($user['imc'] ?? null) ?? session('imc') ?? 0);
        $objectifs = $objectifModel->allOrdered();
        $selected = $this->resolveSelectedObjectif($objectifs, $userIMC);

        $targetIMC = session('imc_target');
        $targetCategoryName = (string) session('imc_target_category_name');
        $regimes = [];
        $sports = [];
        $selectedLabel = $selected['label'];

        if ($targetIMC !== null && (float) $targetIMC > 0) {
            $regimes = $regimeModel->getSuggestedByIMCComparison($userIMC, (float) $targetIMC);
            $selectedLabel = 'IMC ideal : ' . $targetCategoryName;
            $sports = $activiteObjectifModel->getActivitesByIMCComparison($userIMC, (float) $targetIMC, 5);
        } else {
            $filter = trim((string) $this->request->getGet('objectif'));
            if ($filter !== '') {
                foreach ($objectifs as $objectif) {
                    if ((string) ($objectif['nom'] ?? '') === $filter) {
                        $selected['nom'] = $filter;
                        $selected['label'] = $this->objectifLabel($filter);
                        session()->set('objectif_choisi', $filter);
                        break;
                    }
                }
            }

            $regimes = $regimeModel->getSuggestedByObjectif($selected['nom']);
            $selectedLabel = $selected['label'];

            $objectifId = $this->getObjectifIdByNom($objectifs, $selected['nom']);
            $sports = $objectifId > 0 ? $activiteObjectifModel->getActivitesByObjectif($objectifId, 5) : [];
        }

        $remiseGold = $parametreModel->getFloat('remise_gold', 15);
        $isGold = (bool) session('option_gold');

        foreach ($regimes as &$regime) {
            $regimePrices = $regime['prices'] ?? [];
            $bestPrice = null;
            foreach ($regimePrices as $priceRow) {
                $amount = (float) ($priceRow['prix'] ?? 0);
                if ($amount <= 0) {
                    continue;
                }
                if ($bestPrice === null || $amount < $bestPrice) {
                    $bestPrice = $amount;
                }
            }

            $regime['base_price'] = $bestPrice;
            $regime['gold_price'] = $bestPrice !== null ? $bestPrice * (1 - ($remiseGold / 100)) : null;
        }
        unset($regime);

        $payload = [
            'userIMC' => $userIMC,
            'targetIMC' => $targetIMC,
            'selectedLabel' => $selectedLabel,
            'regimes' => $regimes,
            'sports' => $sports,
            'isGold' => $isGold,
            'remiseGold' => $remiseGold,
        ];

        return $this->response
            ->setHeader('Content-Disposition', 'attachment; filename="regimes_suggeres.pdf"')
            ->setContentType('application/pdf')
            ->setBody($this->cataloguePDF($payload));
    }

    private function cataloguePDF(array $payload): string
    {
        $regimes = $payload['regimes'] ?? [];
        $sports = $payload['sports'] ?? [];
        $userIMC = (float) ($payload['userIMC'] ?? 0);
        $targetIMC = $payload['targetIMC'] !== null ? (float) $payload['targetIMC'] : null;
        $selectedLabel = (string) ($payload['selectedLabel'] ?? '');
        $isGold = (bool) ($payload['isGold'] ?? false);
        $remiseGold = (float) ($payload['remiseGold'] ?? 15);

        $pdf = new \FPDF();
        $pdf->SetAutoPageBreak(true, 15);
        $pdf->AddPage();
        $pdf->SetFont('Helvetica', 'B', 16);
        $pdf->Cell(0, 10, $this->pdfText('Resultats des regimes suggeres'), 0, 1, 'C');
        $pdf->Ln(4);

        $pdf->SetFont('Helvetica', '', 11);
        $pdf->Cell(0, 6, $this->pdfText('Objectif : ' . $selectedLabel), 0, 1);
        $pdf->Cell(0, 6, $this->pdfText('IMC actuel : ' . number_format($userIMC, 1, ',', ' ')), 0, 1);
        if ($targetIMC !== null) {
            $pdf->Cell(0, 6, $this->pdfText('IMC cible : ' . number_format($targetIMC, 1, ',', ' ')), 0, 1);
        }
        $pdf->Ln(4);

        $pdf->SetFont('Helvetica', 'B', 13);
        $pdf->Cell(0, 8, $this->pdfText('Regimes suggeres'), 0, 1);

        foreach ($regimes as $index => $regime) {
            $pdf->SetFont('Helvetica', 'B', 12);
            $title = ($index + 1) . '. ' . (string) ($regime['nom'] ?? '');
            $pdf->MultiCell(0, 6, $this->pdfText($title));

            $pdf->SetFont('Helvetica', '', 11);
            $description = (string) ($regime['description'] ?? '');
            if ($description !== '') {
                $pdf->MultiCell(0, 5, $this->pdfText($description));
            }

            $macroLine = sprintf(
                'Viande %s%% | Poisson %s%% | Volaille %s%%',
                (string) ($regime['pourcentage_viande'] ?? 0),
                (string) ($regime['pourcentage_poisson'] ?? 0),
                (string) ($regime['pourcentage_volaille'] ?? 0)
            );
            $pdf->MultiCell(0, 5, $this->pdfText($macroLine));

            $basePrice = $regime['base_price'] ?? null;
            $goldPrice = $regime['gold_price'] ?? null;
            if ($basePrice !== null) {
                $priceLine = 'Prix a partir de ' . number_format((float) $basePrice, 0, ',', ' ') . ' Ar';
                if ($isGold && $goldPrice !== null) {
                    $priceLine .= ' | Gold: ' . number_format((float) $goldPrice, 0, ',', ' ') . ' Ar (-' . $remiseGold . '%)';
                }
                $pdf->MultiCell(0, 5, $this->pdfText($priceLine));
            }

            $pdf->Ln(2);
        }

        if (! empty($sports)) {
            $pdf->Ln(4);
            $pdf->SetFont('Helvetica', 'B', 13);
            $pdf->Cell(0, 8, $this->pdfText('Sports recommandes'), 0, 1);

            foreach ($sports as $sport) {
                $pdf->SetFont('Helvetica', 'B', 11);
                $pdf->MultiCell(0, 5, $this->pdfText((string) ($sport['nom'] ?? '')));
                $pdf->SetFont('Helvetica', '', 10);
                $sportDesc = (string) ($sport['description'] ?? '');
                if ($sportDesc !== '') {
                    $pdf->MultiCell(0, 5, $this->pdfText($sportDesc));
                }
                $calories = (string) ($sport['calories_par_heure'] ?? 0);
                $pdf->MultiCell(0, 5, $this->pdfText('Calories/h : ' . $calories));
                $pdf->Ln(2);
            }
        }

        return $pdf->Output('S');
    }

    private function resolveSelectedObjectif(array $objectifs, float $imc): array
    {
        $selectedNom = trim((string) session('objectif_choisi'));
        if ($selectedNom !== '') {
            foreach ($objectifs as $objectif) {
                if ((string) ($objectif['nom'] ?? '') === $selectedNom) {
                    return [
                        'nom' => $selectedNom,
                        'label' => $this->objectifLabel($selectedNom),
                    ];
                }
            }
        }

        if ($imc > 25) {
            $selectedNom = 'reduction';
        } elseif ($imc < 18.5) {
            $selectedNom = 'augmentation';
        } else {
            $selectedNom = 'equilibre';
        }

        session()->set('objectif_choisi', $selectedNom);

        return [
            'nom' => $selectedNom,
            'label' => $this->objectifLabel($selectedNom),
        ];
    }

    private function objectifLabel(string $objectifNom): string
    {
        if ($objectifNom === 'reduction') {
            return 'Reduire le poids';
        }

        if ($objectifNom === 'augmentation') {
            return 'Augmenter le poids';
        }

        return 'IMC ideal';
    }

    private function getObjectifIdByNom(array $objectifs, string $objectifNom): int
    {
        foreach ($objectifs as $objectif) {
            if ((string) ($objectif['nom'] ?? '') === $objectifNom) {
                return (int) ($objectif['id'] ?? 0);
            }
        }

        return 0;
    }

    private function pdfText(string $value): string
    {
        if ($value === '') {
            return '';
        }

        if (function_exists('iconv')) {
            $converted = iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $value);
            if ($converted !== false) {
                return $converted;
            }
        }

        return utf8_decode($value);
    }
}