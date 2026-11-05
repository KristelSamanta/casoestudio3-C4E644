<?php

namespace Tests\Unit;

use App\Models\Curso;
use App\Models\Grupo;
use App\Models\Docente;
use App\Models\Estudiante;
use DomainException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;


class GrupoTest extends TestCase
{
    use RefreshDatabase;

    public function test_grupo_guardado_se_recupera_con_sus_datos_y_su_curso(): void
    {
        $curso = Curso::create([
            'sigla' => 'IF6100',
            'nombre_curso' => 'Análisis y Diseño de Sistemas',
            'creditos' => 4,
        ]);

        $grupo = Grupo::create([
            'numero_ciclo' => 2,
            'ano' => 2026,
            'numero_grupo' => 1,
            'abierto' => false,
            'curso_id' => $curso->id,
        ]);

        $recuperado = Grupo::find($grupo->id);

        $this->assertNotNull($recuperado);
        $this->assertSame(2, $recuperado->numero_ciclo);
        $this->assertSame(2026, $recuperado->ano);
        $this->assertSame(1, $recuperado->numero_grupo);
        $this->assertSame(false, $recuperado->abierto);
        $this->assertSame('IF6100', $recuperado->curso->sigla);
        $this->assertSame(4, $recuperado->curso->creditos);
    }

    public function test_curso_tiene_tres_grupos_y_solo_dos_estan_abiertos(): void
{
    $curso = Curso::create([
        'sigla' => 'IF6100',
        'nombre_curso' => 'Análisis y Diseño de Sistemas',
        'creditos' => 4,
    ]);

    foreach ([true, true, false] as $indice => $abierto) {
        Grupo::create([
            'numero_ciclo' => 2,
            'ano' => 2026,
            'numero_grupo' => $indice + 1,
            'abierto' => $abierto,
            'curso_id' => $curso->id,
        ]);
    }

    $grupos = $curso->grupos;

    $this->assertCount(3, $grupos);
    $this->assertCount(2, $grupos->where('abierto', true));
    $this->assertSame(
        [$curso->id],
        $grupos->pluck('curso_id')->unique()->values()->all()
    );
}
public function test_grupo_acepta_dos_docentes_y_la_relacion_se_ve_desde_ambos_lados(): void
{
    $curso = Curso::create([
        'sigla' => 'IF6100',
        'nombre_curso' => 'Análisis y Diseño de Sistemas',
        'creditos' => 4,
    ]);

    $grupo = Grupo::create([
        'numero_ciclo' => 2,
        'ano' => 2026,
        'numero_grupo' => 1,
        'abierto' => false,
        'curso_id' => $curso->id,
    ]);

    $docente1 = Docente::create([
        'cod_empleado' => 1001,
        'nombre_empleado' => 'Ana',
        'apellidos_empleado' => 'Mora Vargas',
    ]);

    $docente2 = Docente::create([
        'cod_empleado' => 1002,
        'nombre_empleado' => 'Luis',
        'apellidos_empleado' => 'Rojas Solano',
    ]);

    $grupo->asignarDocente(1001);
    $grupo->asignarDocente(1002);

    $this->assertCount(2, $grupo->fresh()->docentes);
    $this->assertTrue($docente1->fresh()->grupos->contains('id', $grupo->id));
    $this->assertTrue($docente2->fresh()->grupos->contains('id', $grupo->id));
    $this->assertDatabaseCount('docente_grupo', 2);
}
public function test_grupo_rechaza_un_tercer_docente_y_conserva_los_dos_originales(): void
{
    $curso = Curso::create([
        'sigla' => 'IF6100',
        'nombre_curso' => 'Análisis y Diseño de Sistemas',
        'creditos' => 4,
    ]);

    $grupo = Grupo::create([
        'numero_ciclo' => 2,
        'ano' => 2026,
        'numero_grupo' => 1,
        'abierto' => false,
        'curso_id' => $curso->id,
    ]);

    foreach ([1001, 1002, 1003] as $codigo) {
        Docente::create([
            'cod_empleado' => $codigo,
            'nombre_empleado' => 'Docente',
            'apellidos_empleado' => (string) $codigo,
        ]);
    }

    $grupo->asignarDocente(1001);
    $grupo->asignarDocente(1002);

    $this->expectException(DomainException::class);

    $grupo->asignarDocente(1003);
}
public function test_grupo_con_catorce_estudiantes_no_se_abre(): void
{
    $curso = Curso::create([
        'sigla' => 'IF6100',
        'nombre_curso' => 'Análisis y Diseño de Sistemas',
        'creditos' => 4,
    ]);

    $grupo = Grupo::create([
        'numero_ciclo' => 2,
        'ano' => 2026,
        'numero_grupo' => 1,
        'abierto' => false,
        'curso_id' => $curso->id,
    ]);

    $docente = Docente::create([
        'cod_empleado' => 1001,
        'nombre_empleado' => 'Ana',
        'apellidos_empleado' => 'Mora Vargas',
    ]);

    $grupo->asignarDocente(1001);

    $estudiantes = Estudiante::factory()
        ->count(14)
        ->create(['docente_id' => $docente->id]);

    $grupo->estudiantes()->attach($estudiantes->modelKeys());

    $this->assertFalse($grupo->puedeAbrirse());

    try {
        $grupo->abrir();
        $this->fail('Se esperaba una DomainException.');
    } catch (DomainException) {
        $this->assertFalse($grupo->fresh()->abierto);
    }
}
public function test_grupo_con_quince_estudiantes_y_un_docente_se_abre(): void
{
    $curso = Curso::create([
        'sigla' => 'IF6100',
        'nombre_curso' => 'Análisis y Diseño de Sistemas',
        'creditos' => 4,
    ]);

    $grupo = Grupo::create([
        'numero_ciclo' => 2,
        'ano' => 2026,
        'numero_grupo' => 1,
        'abierto' => false,
        'curso_id' => $curso->id,
    ]);

    $docente = Docente::create([
        'cod_empleado' => 1001,
        'nombre_empleado' => 'Ana',
        'apellidos_empleado' => 'Mora Vargas',
    ]);

    $grupo->asignarDocente(1001);

    $estudiantes = Estudiante::factory()
        ->count(15)
        ->create(['docente_id' => $docente->id]);

    $grupo->estudiantes()->attach($estudiantes->modelKeys());

    $this->assertTrue($grupo->puedeAbrirse());

    $grupo->abrir();

    $this->assertTrue($grupo->fresh()->abierto);
}
}
