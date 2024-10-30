<?php

namespace App\Http\Controllers\Pdf;

use App\Http\Controllers\Controller;
use App\Models\Equipment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class EquipmentPdf extends Controller
{
    public function __invoke(Request $request)
    {
        dd($request);
        // Get the query parameters
        $query = $request->query();
        dd($query);
        // Build the query based on the parameters
        $equipmentQuery = Equipment::query()->with(['personnel', 'accountable_officer', 'organization_unit', 'operating_unit_project', 'fund']);

        // Apply filters based on query parameters
        if (isset($query['responsible_person'])) {
            $equipmentQuery->where('personnel_id', $query['responsible_person']);
        }

        if (isset($query['accountable_officer'])) {
            $equipmentQuery->where('accountable_officer_id', $query['accountable_officer']);
        }
        // Add more filters as needed...

        $equipment = $equipmentQuery->get();

        // Generate PDF with filtered data
        $pdf = PDF::loadView('pdf.equipment-list', ['equipments' => $equipment]);

        return $pdf->setPaper('a3', 'landscape')->download('equipment-report.pdf');;
    }
}
