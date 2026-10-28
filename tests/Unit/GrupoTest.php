<?php

namespace Tests\Unit;

use App\Models\Curso;
use App\Models\Docente;
use App\Models\Estudiante;
use App\Models\EstudianteMatriculado;
use App\Models\Grupo;
use Illuminate\Database\Eloquent\Collection;
use PHPUnit\Framework\TestCase;

class GrupoTest extends TestCase
{
    public function test_grupo_contiene_su_informacion_y_relaciones(): void
    {
        $curso = new Curso([
            'sigla' => 'IF6100',
            'nombre_curso' => 'Análisis y Diseño de Sistemas',
            'creditos' => 4,
        ]);

        $docente1 = new Docente([
            'cod_empleado' => 101,
            'nombre_empleado' => 'Ana',
            'apellidos_empleado' => 'Vargas',
        ]);

        $docente2 = new Docente([
            'cod_empleado' => 102,
            'nombre_empleado' => 'Luis',
            'apellidos_empleado' => 'Mora',
        ]);

        $estudiante = new Estudiante([
            'carne' => 'C4E644',
            'nombre' => 'María',
            'apellidos' => 'Solano',
        ]);

        $matricula = new EstudianteMatriculado([
            'nota' => 95.5,
        ]);
        $matricula->setRelation('estudiante', $estudiante);

        $grupo = new Grupo([
            'numero_ciclo' => 1,
            'ano' => 2026,
            'numero_grupo' => 2,
            'abierto' => true,
        ]);

        $grupo->setRelation('curso', $curso);
        $grupo->setRelation('docentes', new Collection([$docente1, $docente2]));
        $grupo->setRelation('matriculas', new Collection([$matricula]));

        $this->assertSame(1, $grupo->numero_ciclo);
        $this->assertSame(2026, $grupo->ano);
        $this->assertSame(2, $grupo->numero_grupo);
        $this->assertTrue($grupo->abierto);
        $this->assertSame('IF6100', $grupo->curso->sigla);
        $this->assertCount(2, $grupo->docentes);
        $this->assertSame('C4E644', $grupo->matriculas->first()->estudiante->carne);
        $this->assertEquals(95.5, $grupo->matriculas->first()->nota);
    }
}
