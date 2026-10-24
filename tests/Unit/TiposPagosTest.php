<?php

namespace Tests\Unit;

use Tests\Support\UnitTester;
use App\Models\TiposPagos;

class TiposPagosTest extends \Codeception\Test\Unit {

    protected UnitTester $tester;
    private TiposPagos $tipoPago;

    protected function _before() {
        $this->tipoPago = new TiposPagos();
    }

    public function testInstanciaTiposPagos() {
        $this->assertInstanceOf(TiposPagos::class, $this->tipoPago);
    }

    public function testGettersYSetters() {
        $this->tipoPago->setId(5);
        $this->tipoPago->setNombre('Zelle');

        $this->assertEquals(5, $this->tipoPago->getId());
        $this->assertEquals('Zelle', $this->tipoPago->getNombre());
    }

    public function testValidarNombreVacio() {
        $this->tipoPago->setNombre('');
        $resultado = $this->tipoPago->validar();
        $this->assertFalse($resultado['valido']);
        $this->assertStringContainsString('obligatorio', $resultado['mensaje']);
    }

    public function testValidarNombreMuyCorto() {
        $this->tipoPago->setNombre('AB');
        $resultado = $this->tipoPago->validar();
        $this->assertFalse($resultado['valido']);
        $this->assertStringContainsString('entre 3 y 30 caracteres', $resultado['mensaje']);
    }

    public function testValidarNombreConNumeros() {
        $this->tipoPago->setNombre('PagoMovil123');
        $resultado = $this->tipoPago->validar();
        $this->assertFalse($resultado['valido']);
        $this->assertStringContainsString('solo acepta letras y espacios', $resultado['mensaje']);
    }

    public function testValidarNombreValido() {
        $this->tipoPago->setNombre('Transferencia Bancaria');
        $resultado = $this->tipoPago->validar();
        $this->assertTrue($resultado['valido']);
    }

    public function testPersistenciaEnBaseDeDatos() {
        $id = $this->tester->haveInDatabase('tipos_pagos', [
            'nombre' => 'Cripto Moneda Test',
            'estado' => 'A'
        ]);

        $this->tester->seeInDatabase('tipos_pagos', [
            'id' => $id,
            'nombre' => 'Cripto Moneda Test'
        ]);

        $registro = $this->tipoPago->traerPorId($id);
        $this->assertNotNull($registro);
        $this->assertEquals('Cripto Moneda Test', $registro->nombre);
    }
}
