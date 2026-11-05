<?php

namespace Tests\Feature;

use App\Models\Curso;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CursoTest extends TestCase
{
    use RefreshDatabase;

    public function test_puede_crear_un_curso_y_guardarlo_en_la_base_de_datos(): void
    {
        $respuesta = $this->postJson('/cursos', [
            'sigla' => 'IF6100',
            'nombre_curso' => 'Análisis y Diseño de Sistemas',
            'creditos' => 4,
        ]);

        $respuesta->assertCreated()
            ->assertJsonPath('data.sigla', 'IF6100')
            ->assertJsonPath('data.nombre_curso', 'Análisis y Diseño de Sistemas')
            ->assertJsonPath('data.creditos', 4);

        $this->assertDatabaseHas('cursos', [
            'sigla' => 'IF6100',
            'nombre_curso' => 'Análisis y Diseño de Sistemas',
            'creditos' => 4,
        ]);
    }
}
