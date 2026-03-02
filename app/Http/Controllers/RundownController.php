<?php

namespace App\Http\Controllers;

use App\Models\Rundown;
use Illuminate\Http\Request;

class RundownController extends Controller
{
    public function index() 
    {
        // Usamos 'with' para traer los segmentos ya ordenados desde la base de datos
        $rundown = Rundown::with(['show', 'segments' => function($q) {
            $q->orderBy('order_index', 'asc');
        }])->first();

        return view('rundown', compact('rundown'));
    }
}