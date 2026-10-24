<?php

namespace Tests\Acceptance;

use Tests\Support\AcceptanceTester;

class RutasProtegidasCest {

    public function usuarioNoAutenticadoEsRedirigidoAlLoginDesdeInicio(AcceptanceTester $I) {
        $I->wantTo('Comprobar que un usuario sin sesión es redirigido a login al intentar acceder a inicio');
        $I->amOnPage('inicio');
        $I->seeInCurrentUrl('login');
        $I->see('Iniciar Sesión', 'h3');
    }

    public function usuarioNoAutenticadoEsRedirigidoAlLoginDesdeModulosProtegidos(AcceptanceTester $I) {
        $I->wantTo('Comprobar redirección al login desde módulos de administración');
        $I->amOnPage('miembros');
        $I->seeInCurrentUrl('login');

        $I->amOnPage('inventario');
        $I->seeInCurrentUrl('login');

        $I->amOnPage('membresias');
        $I->seeInCurrentUrl('login');
    }

    public function rutaInexistenteProtegidaRedirigeAlLogin(AcceptanceTester $I) {
        $I->wantTo('Comprobar que una ruta desconocida solicita autenticación antes de cualquier acceso');
        $I->amOnPage('modulo_desconocido_123');
        $I->seeInCurrentUrl('login');
        $I->see('Iniciar Sesión', 'h3');
    }
}
