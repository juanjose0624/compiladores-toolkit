<?php
/**
 * AFN - Autómata Finito No Determinístico
 * Reconoce cadenas binarias que CONTIENEN la subcadena '01' en algún punto
 *
 * Diferencia clave con el AFD: cada (estado, símbolo) puede llevar
 * a VARIOS estados posibles a la vez (o a ninguno).
 */
class AFN implements Automata
{
    private array $transiciones;
    private string $estadoInicial;
    private array $estadosAceptacion;

    public function __construct()
    {
        $this->transiciones = [
            'q0' => ['0' => ['q0', 'q1'], '1' => ['q0']],
            'q1' => ['1' => ['q2']],
            'q2' => ['0' => ['q2'], '1' => ['q2']],
        ];
        $this->estadoInicial = 'q0';
        $this->estadosAceptacion = ['q2'];
    }

    public function ejecutar(string $cadena): array
    {
        $estadosActuales = [$this->estadoInicial];
        $traza = [];

        $longitud = strlen($cadena);
        for ($i = 0; $i < $longitud; $i++) {
            $simbolo = $cadena[$i];
            $siguientes = [];

            foreach ($estadosActuales as $estado) {
                if (isset($this->transiciones[$estado][$simbolo])) {
                    foreach ($this->transiciones[$estado][$simbolo] as $destino) {
                        if (!in_array($destino, $siguientes, true)) {
                            $siguientes[] = $destino;
                        }
                    }
                }
            }

            $traza[] = [
                'desde' => $estadosActuales,
                'simbolo' => $simbolo,
                'hacia' => $siguientes,
            ];

            $estadosActuales = $siguientes;
            if (empty($estadosActuales)) {
                break;
            }
        }

        $aceptada = count(array_intersect($estadosActuales, $this->estadosAceptacion)) > 0;

        return [
            'tipo' => 'AFN',
            'cadena' => $cadena,
            'traza' => $traza,
            'estadosFinales' => $estadosActuales,
            'aceptada' => $aceptada,
        ];
    }
}