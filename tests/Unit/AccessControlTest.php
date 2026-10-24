<?php
namespace Tests\Unit;
use \Codeception\Test\Unit;

class AccessControlTest extends Unit
{
    // Funcion estructural con multiples ramas logicas
    public function verificarAccesoGym($diasMembresia, $tienePaseDiario) {
        if ($diasMembresia > 0) {
            return "Acceso Permitido: Membresía Activa"; // Rama 1
        } elseif ($tienePaseDiario === true) {
            return "Acceso Permitido: Pase de 1 Día"; // Rama 2
        } else {
            return "Acceso Denegado: Vencido"; // Rama 3
        }
    }

    public function testCoberturaDeRamasDeAcceso()
    {
        // Rama 1
        $resultadoRama1 = $this->verificarAccesoGym(10, false);
        $this->assertEquals("Acceso Permitido: Membresía Activa", $resultadoRama1);

        // Rama 2
        $resultadoRama2 = $this->verificarAccesoGym(0, true);
        $this->assertEquals("Acceso Permitido: Pase de 1 Día", $resultadoRama2);

        // Rama 3
        $resultadoRama3 = $this->verificarAccesoGym(0, false);
        $this->assertEquals("Acceso Denegado: Vencido", $resultadoRama3);
    }
}