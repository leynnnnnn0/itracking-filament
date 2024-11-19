<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UserManualController extends Controller
{
    public function __invoke()
    {
        $path = 'storage\manual\users-manual.docx';
        return response()->download($path);
    }
}
