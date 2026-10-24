<?php

namespace Tests\Acceptance;

use Tests\Support\AcceptanceTester;

class LoginCest {

    public function _before(AcceptanceTester $I) {
        $I->amOnPage('login');
    }

    public function verificarElementosVisualesDelLogin(AcceptanceTester $I) {
        $I->wantTo('Verificar que la pantalla de inicio de sesión tiene los elementos y campos requeridos');
        $I->seeResponseCodeIs(200);
        $I->see('Iniciar Sesión', 'h3');
        $I->seeElement('input#usuario');
        $I->seeElement('input#password');
        $I->seeElement('button[type=submit]');
        $I->seeLink('¿Olvidaste tu clave?', 'login/olvidePassword');
    }

    public function intentarLoginConCamposVacios(AcceptanceTester $I) {
        $I->wantTo('Verificar validación o rechazo al intentar autenticar con campos vacíos');
        $I->amOnPage('login');
        $I->click('button[type=submit]');
        // Debe permanecer en login
        $I->seeInCurrentUrl('login');
    }

    public function intentarLoginConCredencialesInvalidas(AcceptanceTester $I) {
        $I->wantTo('Verificar que el sistema rechaza credenciales incorrectas');
        $I->amOnPage('login');
        $I->fillField('usuario', 'usuario_inexistente_xyz');
        $I->fillField('password', 'clave_invalida_123');
        $I->click('button[type=submit]');
        $I->seeInCurrentUrl('login');
    }

    public function navegarHaciaRecuperarPasswordYVolver(AcceptanceTester $I) {
        $I->wantTo('Navegar hacia la pantalla de recuperar contraseña y regresar');
        $I->click('¿Olvidaste tu clave?');
        $I->seeInCurrentUrl('login/olvidePassword');
        $I->see('Recuperar Contraseña', 'h3');
        $I->seeElement('input#correo_recuperacion');
        $I->see('Volver al inicio de sesión');

        // Hacer click en volver
        $I->click('Volver al inicio de sesión');
        $I->seeInCurrentUrl('login');
        $I->see('Iniciar Sesión', 'h3');
    }
}
