<?php

namespace Tests\Acceptance;

use Tests\Support\AcceptanceTester;

class PagosCest {

    public function accesoNoAutenticadoRedirigeAlLogin(AcceptanceTester $I): void {
        $I->wantTo('Verificar que un usuario sin sesión es redirigido a login al intentar acceder a pagos');
        $I->amOnPage('pagos');
        $I->seeInCurrentUrl('login');
        $I->see('Iniciar Sesión', 'h3');
    }

    public function consultarDetallePagoInvalidoRedirige(AcceptanceTester $I): void {
        $I->wantTo('Verificar que consultar un pago inexistente redirige de vuelta a pagos');
        $I->login();
        $I->amOnPage('pagos/detalles/999999');
        $I->seeInCurrentUrl('pagos');
    }
}
