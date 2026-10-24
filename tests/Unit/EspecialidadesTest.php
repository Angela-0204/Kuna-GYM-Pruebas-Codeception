<?php

namespace Tests\Unit;

use Tests\Support\UnitTester;
use App\Models\Especialidades;

class EspecialidadesTest extends \Codeception\Test\Unit {

    protected UnitTester $tester;
    private Especialidades $especialidad;

    protected function _before() {
        $this->especialidad = new Especialidades();
    }

    public function testInstanciaEspecialidades() {
        $this->assertInstanceOf(Especialidades::class, $this->especialidad);
    }

    public function testGettersYSetters() {
        $this->especialidad->setId(8);
        $this->especialidad->setNombre('CrossFit');
        $this->especialidad->setDescripcion('Entrenamiento de alta intensidad');
        $this->especialidad->setEstado('A');

        $this->assertEquals(8, $this->especialidad->getId());
        $this->assertEquals('CrossFit', $this->especialidad->getNombre());
        $this->assertEquals('Entrenamiento de alta intensidad', $this->especialidad->getDescripcion());
        $this->assertEquals('A', $this->especialidad->getEstado());
    }

    public function testValidarNombreVacio() {
        $this->especialidad->setNombre('');
        $resultado = $this->especialidad->validar();
        $this->assertFalse($resultado['valido']);
        $this->assertStringContainsString('obligatorio', $resultado['mensaje']);
    }

    public function testValidarNombreLongitudInvalida() {
        $this->especialidad->setNombre('AB');
        $resultado = $this->especialidad->validar();
        $this->assertFalse($resultado['valido']);
        $this->assertStringContainsString('entre 3 y 30 caracteres', $resultado['mensaje']);

        $this->especialidad->setNombre(str_repeat('A', 31));
        $resultadoLargo = $this->especialidad->validar();
        $this->assertFalse($resultadoLargo['valido']);
    }

    public function testValidarNombreCaracteresInvalidos() {
        $this->especialidad->setNombre('CrossFit #1!');
        $resultado = $this->especialidad->validar();
        $this->assertFalse($resultado['valido']);
        $this->assertStringContainsString('solo acepta letras, números y signos', $resultado['mensaje']);
    }

    public function testValidarDescripcionDemasiadoLarga() {
        $this->especialidad->setNombre('Pilates');
        $this->especialidad->setDescripcion(str_repeat('A', 256));

        $resultado = $this->especialidad->validar();
        $this->assertFalse($resultado['valido']);
        $this->assertStringContainsString('no puede exceder los 255 caracteres', $resultado['mensaje']);
    }

    public function testValidarEspecialidadValida() {
        $this->especialidad->setNombre('Spinning Pro');
        $this->especialidad->setDescripcion('Clases de ciclismo indoor con música.');

        $resultado = $this->especialidad->validar();
        $this->assertTrue($resultado['valido']);
    }

    public function testPersistenciaEnBaseDeDatos() {
        $id = $this->tester->haveInDatabase('especialidades', [
            'nombre' => 'Yoga Test BD',
            'descripcion' => 'Clases de flexibilidad y relajacion',
            'estado' => 'A'
        ]);

        $this->tester->seeInDatabase('especialidades', [
            'id' => $id,
            'nombre' => 'Yoga Test BD'
        ]);

        $registro = $this->especialidad->obtenerPorId($id);
        $this->assertNotNull($registro);
        $this->assertEquals('Yoga Test BD', $registro->nombre);
    }
}
