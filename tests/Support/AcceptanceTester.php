<?php

declare(strict_types=1);

namespace Tests\Support;

/**
 * Inherited Methods
 * @method void wantTo($text)
 * @method void wantToTest($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method void pause($vars = [])
 *
 * @SuppressWarnings(PHPMD)
 */
class AcceptanceTester extends \Codeception\Actor {
    use _generated\AcceptanceTesterActions;

    /**
     * Define custom actions here
     */
    public function login(string $usuario = 'admin_test', string $password = 'test1234'): void {
        $this->amOnPage('login');
        $this->fillField('usuario', $usuario);
        $this->fillField('password', $password);
        $this->submitForm('#loginForm', [
            'usuario' => $usuario,
            'password' => $password,
            'g-recaptcha-response' => 'test-token'
        ]);
    }
}
