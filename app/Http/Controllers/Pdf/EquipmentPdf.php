<?php

namespace App\Http\Controllers\Pdf;

use App\Http\Controllers\Controller;
use App\Models\Equipment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class EquipmentPdf extends Controller
{
    public function __invoke(Equipment $equipment, $previousPersonnel)
    {

        $pdf = PDF::loadView('pdf.equipment', [
            'equipment' => $equipment,
            'previous_responsible_person' => $previousPersonnel
        ]);

        return $pdf->setPaper('a3', 'landscape')->download('equipment-report.pdf');;
    }
}
