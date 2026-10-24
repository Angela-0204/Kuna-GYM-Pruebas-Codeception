<?php

namespace Tests\Functional;

use Tests\Support\FunctionalTester;
use App\Models\Ventas;

class VentasFunctionalCest {
    private Ventas $ventasModelo;

    public function _before(FunctionalTester $I): void {
        $this->ventasModelo = new Ventas();
    }

    public function verificarInstanciaYConfiguracionVentas(FunctionalTester $I): void {
        $I->wantTo('Verificar inicialización del modelo de ventas y métodos base de configuración');

        $tasaActual = $this->ventasModelo->obtenerTasaActual();
        $I->assertIsArray($tasaActual);
        $I->assertArrayHasKey('tasa', $tasaActual);
        $I->assertArrayHasKey('origen', $tasaActual);

        $primerAnio = $this->ventasModelo->obtenerPrimerAnio();
        $I->assertNotEmpty($primerAnio);
    }

    public function verificarResumenVentasHoy(FunctionalTester $I): void {
        $I->wantTo('Verificar que el resumen diario de ventas retorna estructura consistente');

        $resumen = $this->ventasModelo->obtenerResumenVentasHoy();

        $I->assertIsObject($resumen);
        $I->assertTrue(property_exists($resumen, 'total_ventas'));
        $I->assertTrue(property_exists($resumen, 'total_ingresos'));
        $I->assertTrue(property_exists($resumen, 'total_articulos'));
        $I->assertTrue(property_exists($resumen, 'producto_estrella'));
        $I->assertIsNumeric($resumen->total_ventas);
        $I->assertIsNumeric($resumen->total_ingresos);
    }

    public function verificarPersistenciaVentaYRelacionesEnBaseDeDatos(FunctionalTester $I): void {
        $I->wantTo('Registrar una venta y verificar su persistencia relacional con comprobante y pago');

        $idTipoPago = $I->haveInDatabase('tipos_pagos', [
            'nombre' => 'Punto de Venta Test',
            'estado' => 'A'
        ]);

        $idMoneda = $I->haveInDatabase('monedas', [
            'nombre' => 'USD',
            'estado' => 'A'
        ]);

        $idMiembro = $I->haveInDatabase('miembros', [
            'nombre' => 'Laura',
            'apellido' => 'Mendez',
            'telefono' => '04245551234',
            'correo' => 'laura.ventas@correo.com',
            'documento' => 'V-20304050',
            'password' => password_hash('123456', PASSWORD_BCRYPT),
            'codigo_acceso' => 'TEST-VT-01',
            'estado' => 'A'
        ]);

        $idComprobante = $I->haveInDatabase('comprobantes', [
            'id_miembro' => $idMiembro,
            'concepto' => 'Venta de Mostrador Test',
            'estado' => 'C',
            'recibo_enviado' => 0
        ]);

        $idVenta = $I->haveInDatabase('ventas', [
            'id_comprobante' => $idComprobante,
            'fecha' => date('Y-m-d H:i:s'),
            'estado' => 'A'
        ]);

        $idPago = $I->haveInDatabase('pagos', [
            'id_comprobante' => $idComprobante,
            'monto' => 15.00,
            'id_moneda' => $idMoneda,
            'id_tipo_pago' => $idTipoPago,
            'tasa' => 1.00,
            'fecha_pago' => date('Y-m-d H:i:s'),
            'estado' => 'C'
        ]);

        // Aserciones en BD
        $I->seeInDatabase('ventas', [
            'id' => $idVenta,
            'id_comprobante' => $idComprobante,
            'estado' => 'A'
        ]);

        $I->seeInDatabase('comprobantes', [
            'id' => $idComprobante,
            'id_miembro' => $idMiembro,
            'concepto' => 'Venta de Mostrador Test'
        ]);

        $I->seeInDatabase('pagos', [
            'id' => $idPago,
            'id_comprobante' => $idComprobante,
            'monto' => 15.00
        ]);

        $ventaConsultada = $this->ventasModelo->obtenerVentaPorId($idVenta);
        $I->assertNotNull($ventaConsultada);
        $I->assertEquals($idVenta, $ventaConsultada->id);
        $I->assertEquals('A', $ventaConsultada->estado);
        $I->assertEquals('Laura', $ventaConsultada->cliente_nombre);
    }

    public function consultarVentaInexistenteRetornaVacio(FunctionalTester $I): void {
        $I->wantTo('Comprobar que consultar un ID de venta que no existe retorne vacío o falso');
        $resultado = $this->ventasModelo->obtenerVentaPorId(999999);
        $I->assertEmpty($resultado);
    }

    public function verificarTiposPagoDisponiblesParaVenta(FunctionalTester $I): void {
        $I->wantTo('Comprobar que el módulo de ventas obtiene los métodos de pago habilitados');
        $tipos = $this->ventasModelo->obtenerTiposPago();
        $I->assertIsArray($tipos);
    }
}
