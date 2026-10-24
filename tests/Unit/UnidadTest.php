<?php

namespace Tests\Unit;

use Tests\Support\UnitTester;
use App\Models\Unidad;

class UnidadTest extends \Codeception\Test\Unit {

    protected UnitTester $tester;
    private Unidad $unidad;

    protected function _before() {
        $this->unidad = new Unidad();
    }

    public function testInstanciaUnidad() {
        $this->assertInstanceOf(Unidad::class, $this->unidad);
    }

    public function testGettersYSetters() {
        $this->unidad->setId(10);
        $this->unidad->setNombre('Kilogramo');
        $this->unidad->setCantidad(5);
        $this->unidad->setEstado('A');

        $this->assertEquals(10, $this->unidad->getId());
        $this->assertEquals('Kilogramo', $this->unidad->getNombre());
        $this->assertEquals(5, $this->unidad->getCantidad());
        $this->assertEquals('A', $this->unidad->getEstado());
    }

    public function testValidarNombreVacio() {
        $this->unidad->setNombre('');
        $this->unidad->setCantidad(1);

        $resultado = $this->unidad->validar();
        $this->assertFalse($resultado['valido']);
        $this->assertStringContainsString('obligatorio', $resultado['mensaje']);
    }

    public function testValidarNombreCorto() {
        $this->unidad->setNombre('A');
        $this->unidad->setCantidad(1);

        $resultado = $this->unidad->validar();
        $this->assertFalse($resultado['valido']);
        $this->assertStringContainsString('al menos 2 caracteres', $resultado['mensaje']);
    }

    public function testValidarNombreConCaracteresInvalidos() {
        $this->unidad->setNombre('Caja@#');
        $this->unidad->setCantidad(1);

        $resultado = $this->unidad->validar();
        $this->assertFalse($resultado['valido']);
        $this->assertStringContainsString('caracteres no permitidos', $resultado['mensaje']);
    }

    public function testValidarCantidadInvalida() {
        $this->unidad->setNombre('Caja');
        $this->unidad->setCantidad('no-numero');

        $resultado = $this->unidad->validar();
        $this->assertFalse($resultado['valido']);
        $this->assertStringContainsString('entero válido', $resultado['mensaje']);
    }

    public function testValidarCantidadMenorAUno() {
        $this->unidad->setNombre('Caja');
        $this->unidad->setCantidad(0);

        $resultado = $this->unidad->validar();
        $this->assertFalse($resultado['valido']);
        $this->assertStringContainsString('mayor o igual a 1', $resultado['mensaje']);
    }

    public function testValidarExitosoFormateaCapitalizacion() {
        $this->unidad->setNombre('paquete grande');
        $this->unidad->setCantidad('10');

        $resultado = $this->unidad->validar();
        $this->assertTrue($resultado['valido']);
        $this->assertEquals('Paquete Grande', $this->unidad->getNombre());
        $this->assertSame(10, $this->unidad->getCantidad());
    }

    public function testPersistenciaEnBaseDeDatos() {
        $id = $this->tester->haveInDatabase('unidad_medida', [
            'nombre' => 'Unidad Test BD',
            'cantidad' => 1,
            'estado' => 'A'
        ]);

        $this->tester->seeInDatabase('unidad_medida', [
            'id' => $id,
            'nombre' => 'Unidad Test BD'
        ]);

        $unidadDb = $this->unidad->obtenerPorId($id);
        $this->assertNotNull($unidadDb);
        $this->assertEquals('Unidad Test BD', $unidadDb->nombre);
    }
}
