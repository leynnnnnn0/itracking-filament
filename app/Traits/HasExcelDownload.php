<?php

namespace App\Traits;

use Maatwebsite\Excel\Facades\Excel;

trait HasExcelDownload
{

    protected $query;
    public function __construct($query)
    {
        $this->query = $query;
    }

    public function collection()
    {
        return $this->query->get();
    }
}
