<?php

namespace Tests\Unit;

use Tests\Support\UnitTester;
use App\Models\Membresias;

class MembresiasTest extends \Codeception\Test\Unit {
    protected UnitTester $tester;
    private Membresias $membresia;

    protected function _before() {
        $this->membresia = new Membresias();
    }

    public function testInstanciaYConsultaMembresias() {
        $this->assertInstanceOf(Membresias::class, $this->membresia);

        $lista = $this->membresia->traerTodo();
        $this->assertIsArray($lista);
    }

    public function testVerificarMembresiaEnBaseDeDatos() {

        $id = $this->tester->haveInDatabase('planes_membresias', [
            'nombre' => 'Plan VIP Test',
            'precio' => 35.00,
            'duracion_dias' => 30,
            'limite_personas' => 1,
            'estado' => 'A'
        ]);

        //verificar que efectivamente está en gym_app_test
        $this->tester->seeInDatabase('planes_membresias', [
            'id' => $id,
            'nombre' => 'Plan VIP Test'
        ]);

        $this->assertTrue(true);
    }
}
