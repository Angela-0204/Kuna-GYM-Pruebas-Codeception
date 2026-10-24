<?php

namespace Tests\Functional;

use Tests\Support\FunctionalTester;
use App\Models\Pagos;
use App\Models\Comprobantes;
use App\Models\TiposPagos;
use App\Models\Monedas;

class PagosFunctionalCest {
    private Pagos $pagoModelo;

    public function _before(FunctionalTester $I): void {
        $this->pagoModelo = new Pagos();
    }

    public function verificarInstanciaYAtributosDePago(FunctionalTester $I): void {
        $I->wantTo('Verificar la correcta inicialización y asignación de atributos en el modelo de pagos');

        $this->pagoModelo->setId(10);
        $this->pagoModelo->setMonto(25.50);
        $this->pagoModelo->setEstado('C');
        $this->pagoModelo->setTasa(36.50);
        $this->pagoModelo->setFecha_pago('2026-10-22 10:00:00');

        $I->assertEquals(10, $this->pagoModelo->getId());
        $I->assertEquals(25.50, $this->pagoModelo->getMonto());
        $I->assertEquals('C', $this->pagoModelo->getEstado());
        $I->assertEquals(36.50, $this->pagoModelo->getTasa());
        $I->assertEquals('2026-10-22 10:00:00', $this->pagoModelo->getFecha_pago());
    }

    public function validarRechazoAlRegistrarPagoSinMiembro(FunctionalTester $I): void {
        $I->wantTo('Comprobar que el flujo de registro de pagos exige obligatoriamente un miembro');

        $datosSuscripcion = [
            'id_miembro' => null,
            'id_membresia' => 1
        ];

        $datosPago = [
            'id_tipo_pago' => 1,
            'referencia' => 'REF-SIN-MIEMBRO',
            'pago_id_moneda' => 1
        ];

        $resultado = $this->pagoModelo->registrarPagoCompleto($datosSuscripcion, $datosPago);

        $I->assertIsArray($resultado);
        $I->assertFalse($resultado['success']);
        $I->assertEquals('warning', $resultado['tipo']);
        $I->assertStringContainsString('miembro', strtolower($resultado['mensaje']));
    }

    public function verificarPersistenciaCompletaDePagoYComprobante(FunctionalTester $I): void {
        $I->wantTo('Registrar y comprobar en base de datos la integridad de un pago con su comprobante y miembro');

        $idTipoPago = $I->haveInDatabase('tipos_pagos', [
            'nombre' => 'Transferencia Functional Test',
            'estado' => 'A'
        ]);

        $idMoneda = $I->haveInDatabase('monedas', [
            'nombre' => 'USD',
            'estado' => 'A'
        ]);

        $idMiembro = $I->haveInDatabase('miembros', [
            'nombre' => 'Carlos',
            'apellido' => 'Perez',
            'telefono' => '04141234567',
            'correo' => 'carlos.functional@correo.com',
            'documento' => 'V-99887766',
            'password' => password_hash('123456', PASSWORD_BCRYPT),
            'codigo_acceso' => 'TEST-COD-01',
            'estado' => 'A'
        ]);

        $idComprobante = $I->haveInDatabase('comprobantes', [
            'id_miembro' => $idMiembro,
            'concepto' => 'Pago Membresía Functional Test',
            'estado' => 'C',
            'recibo_enviado' => 0
        ]);

        $idPago = $I->haveInDatabase('pagos', [
            'id_comprobante' => $idComprobante,
            'monto' => 30.00,
            'id_moneda' => $idMoneda,
            'id_tipo_pago' => $idTipoPago,
            'tasa' => 1.00,
            'fecha_pago' => date('Y-m-d H:i:s'),
            'estado' => 'C'
        ]);

        $I->seeInDatabase('pagos', [
            'id' => $idPago,
            'id_comprobante' => $idComprobante,
            'monto' => 30.00,
            'estado' => 'C'
        ]);

        $I->seeInDatabase('comprobantes', [
            'id' => $idComprobante,
            'id_miembro' => $idMiembro,
            'estado' => 'C'
        ]);
    }

    public function verificarAnulacionDePagoEnBaseDeDatos(FunctionalTester $I): void {
        $I->wantTo('Verificar que el proceso de anulación de un pago actualiza correctamente su estado a Anulado (A)');

        $idMoneda = $I->haveInDatabase('monedas', [
            'nombre' => 'USD',
            'estado' => 'A'
        ]);

        $idTipoPago = $I->haveInDatabase('tipos_pagos', [
            'nombre' => 'Efectivo Functional Test',
            'estado' => 'A'
        ]);

        $idMiembro = $I->haveInDatabase('miembros', [
            'nombre' => 'Maria',
            'apellido' => 'Gomez',
            'telefono' => '04129876543',
            'correo' => 'maria.functional@correo.com',
            'documento' => 'V-11223344',
            'password' => password_hash('123456', PASSWORD_BCRYPT),
            'codigo_acceso' => 'TEST-COD-02',
            'estado' => 'A'
        ]);

        $idComprobante = $I->haveInDatabase('comprobantes', [
            'id_miembro' => $idMiembro,
            'concepto' => 'Pago Para Anular Functional',
            'estado' => 'C',
            'recibo_enviado' => 0
        ]);

        $idPago = $I->haveInDatabase('pagos', [
            'id_comprobante' => $idComprobante,
            'monto' => 45.00,
            'id_moneda' => $idMoneda,
            'id_tipo_pago' => $idTipoPago,
            'tasa' => 1.00,
            'fecha_pago' => date('Y-m-d H:i:s'),
            'estado' => 'C'
        ]);

        $resultado = $this->pagoModelo->anularPago($idPago);
        $I->assertTrue($resultado);
        $I->seeInDatabase('pagos', [
            'id' => $idPago,
            'estado' => 'A'
        ]);
    }

    public function verificarMetricasEstadisticasDePagos(FunctionalTester $I): void {
        $I->wantTo('Verificar que las funciones de cálculo de estadísticas de pagos retornan valores numéricos consistentes');

        $cantPagos = $this->pagoModelo->cantidadPagosMensuales();
        $I->assertIsNumeric($cantPagos);

        $ingresosHoy = $this->pagoModelo->ingresosHoy();
        $I->assertIsNumeric($ingresosHoy);

        $ingresosMes = $this->pagoModelo->ingresosMes();
        $I->assertIsNumeric($ingresosMes);
    }
}
