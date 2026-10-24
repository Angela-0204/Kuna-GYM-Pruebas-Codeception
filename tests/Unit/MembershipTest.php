<?php
namespace Tests\Unit;
use \Codeception\Test\Unit;
use DateTime;

class MembershipTest extends Unit
{
public function calcularDiasRestantes($fechaVencimiento) {

    $hoy = new DateTime(date('Y-m-d')); // netamente fecha, sin hora,ojito
    $vencimiento = new DateTime($fechaVencimiento);
    
    $diferencia = $hoy->diff($vencimiento);
    return (int) $diferencia->format('%r%a');
}

   // Funcion interna (Caja Blanca) 
   /* public function calcularDiasRestantes($fechaVencimiento) {
        $hoy = new DateTime();
        $vencimiento = new DateTime($fechaVencimiento);
        
        // Calcula la diferencia entre la fecha de hoy y el vencimiento
        $diferencia = $hoy->diff($vencimiento);
        
        // format('%r%a') devuelve el total de días incluyendo el signo (positivo o negativo)
        return (int) $diferencia->format('%r%a');
    }
*/

    public function testCalculoDeDiasExactos()
    {
        // Preparar: Creamos una fecha que vence en exactamente 15 días
        $fechaFutura = new DateTime();
        $fechaFutura->modify('+15 days');
        $fechaString = $fechaFutura->format('Y-m-d');

        // Ejecutar: Pasamos el string de la fecha a la función
        $resultado = $this->calcularDiasRestantes($fechaString);

        // Afirmar: Verificamos que el código interno devuelva el número 15
        $this->assertEquals(15, $resultado, "El algoritmo debe retornar 15 días");
    }

    
}