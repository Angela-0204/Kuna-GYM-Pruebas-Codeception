<?php

namespace Tests\Acceptance;

use Tests\Support\AcceptanceTester;

class VentasCest {

    public function accesoNoAutenticadoRedirigeAlLogin(AcceptanceTester $I): void {
        $I->wantTo('Verificar que un usuario sin sesión es redirigido a login al intentar acceder a ventas');
        $I->amOnPage('ventas');
        $I->seeInCurrentUrl('login');
        $I->see('Iniciar Sesión', 'h3');
    }

    public function verificarEstructuraYComponentesModuloVentas(AcceptanceTester $I): void {
        $I->wantTo('Verificar componentes visuales e interfaz del módulo de ventas tras autenticación');
        $I->login();
        $I->amOnPage('ventas');
        $I->seeResponseCodeIs(200);

        // Encabezado
        $I->see('Punto de Venta', 'h2');
        $I->see('Gestiona las ventas de productos de mostrador y tienda', 'p');
        $I->seeElement('#btnNuevaVenta');

        // Tarjetas estadísticas de ventas
        $I->see('Ventas Hoy');
        $I->see('Ingresos Hoy');
        $I->see('Artículos Vendidos');
        $I->see('Producto Estrella');
        $I->seeElement('#stat-ventas-hoy');
        $I->seeElement('#stat-ingresos-hoy');
        $I->seeElement('#stat-articulos-vendidos');
        $I->seeElement('#stat-producto-estrella');

        // Tabla y filtros de período
        $I->see('Listado de Ventas', 'h5');
        $I->seeElement('#tablaVentas');
        $I->seeElement('#filtroMesVentas');
        $I->seeElement('#filtroAnioVentas');

        // Columnas principales de la tabla
        $I->see('Ticket', 'th');
        $I->see('Artículos', 'th');
        $I->see('Total', 'th');
        $I->see('Fecha y Hora', 'th');
        $I->see('Estado', 'th');
        $I->see('Acciones', 'th');
    }

    public function consultarDetalleVentaInvalidaRedirige(AcceptanceTester $I): void {
        $I->wantTo('Verificar que consultar una venta inexistente redirige de vuelta al listado de ventas');
        $I->login();
        $I->amOnPage('ventas/detalles/999999');
        $I->seeInCurrentUrl('ventas');
    }
}
