<?php

namespace Tests\Unit;

use Tests\Support\UnitTester;
use App\Traits\ValidacionesTraits;

class ValidacionesTest extends \Codeception\Test\Unit {

    protected UnitTester $tester;
    private $validador;

    protected function _before() {
        $this->validador = new class {
            use ValidacionesTraits;
        };
    }

    public function testValidarTexto() {
        $this->assertTrue((bool)$this->validador->validarTexto('Gimnasio'));
        $this->assertTrue((bool)$this->validador->validarTexto('Entrenamiento 123, funcional.'));
        $this->assertTrue((bool)$this->validador->validarTexto('Áéíóú Ñandú - Prueba.'));
        $this->assertFalse((bool)$this->validador->validarTexto('Texto con @ caracteres inválidos!'));
        $this->assertFalse((bool)$this->validador->validarTexto(''));
    }

    public function testValidarSoloTexto() {
        $this->assertTrue((bool)$this->validador->validarSoloTexto('Juan Perez'));
        $this->assertTrue((bool)$this->validador->validarSoloTexto('Ángela Núñez'));
        $this->assertFalse((bool)$this->validador->validarSoloTexto('Juan 123'));
        $this->assertFalse((bool)$this->validador->validarSoloTexto('Juan, Perez'));
        $this->assertFalse((bool)$this->validador->validarSoloTexto(''));
    }

    public function testValidarNombreRol() {
        $this->assertTrue($this->validador->validarNombreRol('Administrador'));
        $this->assertTrue($this->validador->validarNombreRol('Entrenador Principal'));
        $this->assertFalse($this->validador->validarNombreRol('Admin123')); // No acepta números
        $this->assertFalse($this->validador->validarNombreRol(str_repeat('A', 35))); // Más de 30 caracteres
    }

    public function testValidarDescripcion() {
        $this->assertTrue((bool)$this->validador->validarDescripcion('Esta es una descripción válida con acentos, éxito, fuerza y constancia.'));
        $this->assertFalse((bool)$this->validador->validarDescripcion('Descripción con carácteres prohibidos <>$$:'));
        $this->assertFalse((bool)$this->validador->validarDescripcion(''));
    }

    public function testValidarEmail() {
        $this->assertTrue($this->validador->validarEmail('test@gimnasio.com'));
        $this->assertTrue($this->validador->validarEmail('usuario.nombre+tag@dominio.com.ve'));
        $this->assertFalse($this->validador->validarEmail('correo-invalido'));
        $this->assertFalse($this->validador->validarEmail('usuario@'));
        $this->assertFalse($this->validador->validarEmail('@dominio.com'));
        $this->assertFalse($this->validador->validarEmail(str_repeat('a', 45) . '@correo.com')); // > 50 chars
    }

    public function testValidarEntero() {
        $this->assertTrue((bool)$this->validador->validarEntero('12345'));
        $this->assertTrue((bool)$this->validador->validarEntero('0'));
        $this->assertFalse((bool)$this->validador->validarEntero('12.34'));
        $this->assertFalse((bool)$this->validador->validarEntero('-5'));
        $this->assertFalse((bool)$this->validador->validarEntero('abc'));
    }

    public function testValidarDecimal() {
        $this->assertTrue((bool)$this->validador->validarDecimal('123.45'));
        $this->assertTrue((bool)$this->validador->validarDecimal('10.5'));
        $this->assertTrue((bool)$this->validador->validarDecimal('500'));
        $this->assertFalse((bool)$this->validador->validarDecimal('12.345')); // Más de dos decimales
        $this->assertFalse((bool)$this->validador->validarDecimal('-10.50'));
        $this->assertFalse((bool)$this->validador->validarDecimal('texto'));
    }

    public function testValidarTelefono() {
        $this->assertTrue($this->validador->validarTelefono('04141234567'));
        $this->assertTrue($this->validador->validarTelefono('0412 123 4567'));
        $this->assertTrue($this->validador->validarTelefono('0424-1234567'));
        $this->assertTrue($this->validador->validarTelefono('+584141234567'));
        $this->assertFalse($this->validador->validarTelefono('12345')); // Demasiado corto
        $this->assertFalse($this->validador->validarTelefono('abcdefghijk'));
    }

    public function testValidarCedula() {
        $this->assertTrue((bool)$this->validador->validarCedula('1234567'));
        $this->assertTrue((bool)$this->validador->validarCedula('12345678'));
        $this->assertFalse((bool)$this->validador->validarCedula('123456')); // 6 dígitos (inválida)
        $this->assertFalse((bool)$this->validador->validarCedula('123456789')); // 9 dígitos (inválida)
        $this->assertFalse((bool)$this->validador->validarCedula('V-12345678')); // No debe tener letras
    }

    public function testValidarRif() {
        $this->assertTrue($this->validador->validarRif('J-12345678-9'));
        $this->assertTrue($this->validador->validarRif('V123456789'));
        $this->assertTrue($this->validador->validarRif('G-12345678'));
        $this->assertFalse($this->validador->validarRif('X-12345678-0')); // Letra inicial no válida
        $this->assertFalse($this->validador->validarRif('12345678-9')); // Sin prefijo
    }

    public function testLimpiar() {
        $entrada = "  <script>alert('hack');</script> Juan & Pedro's  ";
        $salida = $this->validador->limpiar($entrada);
        $this->assertEquals("&lt;script&gt;alert(&#039;hack&#039;);&lt;/script&gt; Juan &amp; Pedro&#039;s", $salida);
    }

    public function testValidarDatosEvaluacion() {
        $datosValidos = [
            'id_miembro' => '1',
            'id_entrenador' => '2',
            'id_dieta' => '3',
            'id_rutina' => '4',
            'peso_kg' => '75.50',
            'altura_cm' => '178.00',
            'brazo' => '35.00',
            'cintura' => '80.00',
            'cadera' => '95.00',
            'piernas' => '55.00',
            'observaciones_generales' => 'Buen progreso inicial'
        ];

        $resultado = $this->validador->validarDatos($datosValidos);
        $this->assertArrayNotHasKey('error', $resultado);
        $this->assertSame(1, $resultado['id_miembro']);
        $this->assertSame(75.5, $resultado['peso_kg']);

        // Faltan campos requeridos
        $datosIncompletos = ['id_miembro' => '1'];
        $resultadoIncompleto = $this->validador->validarDatos($datosIncompletos);
        $this->assertArrayHasKey('error', $resultadoIncompleto);

        // Caso actualización sin id_evaluacion
        $resultadoActualizacionSinId = $this->validador->validarDatos($datosValidos, true);
        $this->assertArrayHasKey('error', $resultadoActualizacionSinId);

        // Caso actualización con id_evaluacion
        $datosValidos['id_evaluacion'] = '10';
        $resultadoActualizacion = $this->validador->validarDatos($datosValidos, true);
        $this->assertArrayNotHasKey('error', $resultadoActualizacion);
    }
}
