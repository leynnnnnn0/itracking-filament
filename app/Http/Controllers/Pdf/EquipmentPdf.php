<?php

namespace App\Http\Controllers\Pdf;

use App\Http\Controllers\Controller;
use App\Models\Equipment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class EquipmentPdf extends Controller
{
    public function __invoke(Equipment $equipment, $previousPersonnel, $previousAccountableOfficer)
    {
        $newResponsiblePerson = $equipment->personnel->full_name !== $previousPersonnel ? true : false;
        $newAccountableOfficer =  $equipment->accountable_officer->full_name !== $previousAccountableOfficer ? true : false;

        $pdf = PDF::loadView('pdf.equipment', [
            'equipment' => $equipment,
            'previous_responsible_person' => $previousPersonnel,
            'previousAccountableOfficer' => $previousAccountableOfficer,
            'newAccountableOfficer' => $newAccountableOfficer,
            'newResponsiblePerson' => $newResponsiblePerson
        ]);

        return $pdf->setPaper('a3', 'landscape')->download('equipment-report.pdf');;
    }
}
