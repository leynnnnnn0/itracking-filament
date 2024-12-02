<?php

namespace App\Traits;

use Maatwebsite\Excel\Facades\Excel;

trait HasExcelDownload
{

    abstract function getReturnModel();
    public function exportAsExcel()
    {
        $query = $this->getFilteredTableQuery();
        $this->applySortingToTableQuery($query);
        return $this->getReturnModel();
    }
}
