<?php
/**
 * AFD - Autómata Finito Determinístico
 * Reconoce cadenas binarias que TERMINAN en '01'
 *
 * Cada estado tiene EXACTAMENTE una transición por símbolo.
 */
class AFD implements Automata
{
    private array $transiciones;
    private string $estadoInicial;
    private array $estadosAceptacion;

    public function __construct()
    {
        $this->transiciones = [
            'q0' => ['0' => 'q1', '1' => 'q0'],
            'q1' => ['0' => 'q1', '1' => 'q2'],
            'q2' => ['0' => 'q1', '1' => 'q0'],
        ];
        $this->estadoInicial = 'q0';
        $this->estadosAceptacion = ['q2'];
    }

    /**
     * Ejecuta el autómata sobre una cadena y devuelve
     * el resultado junto con la traza paso a paso.
     */
    public function ejecutar(string $cadena): array
    {
        $estadoActual = $this->estadoInicial;
        $traza = [];
        $valida = true;

        $longitud = strlen($cadena);
        for ($i = 0; $i < $longitud; $i++) {
            $simbolo = $cadena[$i];

            if (!isset($this->transiciones[$estadoActual][$simbolo])) {
                $valida = false;
                $traza[] = [
                    'desde' => $estadoActual,
                    'simbolo' => $simbolo,
                    'hacia' => null,
                    'error' => "Símbolo '$simbolo' no reconocido",
                ];
                break;
            }

            $siguiente = $this->transiciones[$estadoActual][$simbolo];
            $traza[] = [
                'desde' => $estadoActual,
                'simbolo' => $simbolo,
                'hacia' => $siguiente,
                'error' => null,
            ];
            $estadoActual = $siguiente;
        }

        $aceptada = $valida && in_array($estadoActual, $this->estadosAceptacion, true);

        return [
            'tipo' => 'AFD',
            'cadena' => $cadena,
            'traza' => $traza,
            'estadoFinal' => $estadoActual,
            'aceptada' => $aceptada,
        ];
    }
}