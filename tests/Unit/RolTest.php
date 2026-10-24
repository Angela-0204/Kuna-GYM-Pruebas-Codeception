<?php

namespace Tests\Unit;

use Tests\Support\UnitTester;
use App\Models\Rol;

class RolTest extends \Codeception\Test\Unit {

    protected UnitTester $tester;
    private Rol $rol;

    protected function _before() {
        $this->rol = new Rol();
    }

    public function testInstanciaRol() {
        $this->assertInstanceOf(Rol::class, $this->rol);
    }

    public function testGettersYSetters() {
        $this->rol->setId(3);
        $this->rol->setNombre('Instructor');

        $this->assertEquals(3, $this->rol->getId());
        $this->assertEquals('Instructor', $this->rol->getNombre());
    }

    public function testValidarNombreVacio() {
        $this->rol->setNombre('');
        $resultado = $this->rol->validar();
        $this->assertFalse($resultado['valido']);
        $this->assertStringContainsString('obligatorio', $resultado['mensaje']);
    }

    public function testValidarNombreConNumeros() {
        $this->rol->setNombre('Admin123');
        $resultado = $this->rol->validar();
        $this->assertFalse($resultado['valido']);
        $this->assertStringContainsString('no se aceptan números', $resultado['mensaje']);
    }

    public function testValidarNombreLongitudMayorA30() {
        $this->rol->setNombre(str_repeat('A', 35));
        $resultado = $this->rol->validar();
        $this->assertFalse($resultado['valido']);
        $this->assertStringContainsString('excede los 30 caracteres', $resultado['mensaje']);
    }

    public function testPersistenciaRolEnSeguridadBD() {
        $nombreRol = 'Rol Pruebas Unitarias';
        $permisos = ['ver_usuarios'];

        $guardado = $this->rol->guardarConPermisos($nombreRol, $permisos);
        $this->assertTrue($guardado);

        $roles = $this->rol->obtenerTodos();
        $this->assertNotEmpty($roles);

        $rolEncontrado = null;
        foreach ($roles as $r) {
            if ($r->nombre === $nombreRol) {
                $rolEncontrado = $r;
                break;
            }
        }

        $this->assertNotNull($rolEncontrado);
        $this->assertEquals($nombreRol, $rolEncontrado->nombre);

        // Obtener por ID y verificar permisos
        $rolConPermisos = $this->rol->obtenerPorId($rolEncontrado->id);
        $this->assertNotNull($rolConPermisos);
        $this->assertContains('ver_usuarios', $rolConPermisos->permisos);

        // Limpiar registro de prueba
        $resultadoEliminar = $this->rol->eliminar($rolEncontrado->id);
        $this->assertTrue($resultadoEliminar['success']);
    }
}
