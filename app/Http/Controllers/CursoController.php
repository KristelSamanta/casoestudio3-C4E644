<?php

namespace App\Http\Controllers;

use App\Models\Curso;
use Illuminate\Http\Request;

class CursoController extends Controller
{
    public function store(Request $request)
    {
        $datos = $request->validate([
            'sigla' => ['required', 'string', 'max:255', 'unique:cursos,sigla'],
            'nombre_curso' => ['required', 'string', 'max:255'],
            'creditos' => ['required', 'integer', 'min:1'],
        ]);

        $curso = Curso::create($datos);

        return response()->json(['data' => $curso], 201);
    }
}
