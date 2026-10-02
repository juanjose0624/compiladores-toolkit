<?php
// Controlador: recibe el formulario, llama a la lógica y prepara datos para la vista.
require __DIR__ . '/src/notaciones.php';
require __DIR__ . '/vista.php';

$expr = $_POST['expr'] ?? '';
$modo = $_POST['modo'] ?? 'infija';
$res = null;
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $res = convertir($expr, $modo);
    } catch (InvalidArgumentException $e) {
        $error = $e->getMessage();
    }
}

// Ejercicios de ejemplo: [notación de entrada, expresión]. Los resultados se calculan con convertir().
$listaEjemplos = [
    ['infija',  '(A+B)*C-D/E'],
    ['infija',  'A^B^C'],
    ['infija',  'A-(B-C)'],
    ['infija',  '2^3^2-(4-1)-1'],
    ['infija',  '3.5*x+10'],
    ['infija',  'A/(B*C)'],
    ['prefija', '+ A * B C'],
    ['prefija', '- * + A B C / D E'],
    ['posfija', 'A B C - -'],
    ['posfija', 'A B + C * D E / -'],
];
$ejemplos = [];
foreach ($listaEjemplos as [$m, $e]) {
    $ejemplos[] = ['modo' => $m, 'expr' => $e, 'res' => convertir($e, $m)];
}

renderVista($expr, $modo, $res, $error, $ejemplos);