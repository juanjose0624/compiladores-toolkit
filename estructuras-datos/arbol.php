<?php
// Taller - Arbol Binario de Busqueda
// Insercion + recorridos inorden, preorden, posorden

class Nodo {
    public int $valor;
    public ?Nodo $izquierda = null;
    public ?Nodo $derecha = null;

    // posicion para dibujar el nodo en el SVG
    public float $x = 0;
    public float $y = 0;

    public function __construct(int $valor) {
        $this->valor = $valor;
    }
}

class ArbolBinario {
    public ?Nodo $raiz = null;
    private int $contadorX = 0;

    public function insertar(int $valor): void {
        $this->raiz = $this->insertarNodo($this->raiz, $valor);
    }

    private function insertarNodo(?Nodo $nodo, int $valor): Nodo {
        if ($nodo === null) {
            return new Nodo($valor);
        }

        if ($valor < $nodo->valor) {
            $nodo->izquierda = $this->insertarNodo($nodo->izquierda, $valor);
        } else {
            $nodo->derecha = $this->insertarNodo($nodo->derecha, $valor);
        }

        return $nodo;
    }

    // Izquierda - Nodo - Derecha
    public function inorden(?Nodo $nodo, array &$resultado): void {
        if ($nodo === null) return;
        $this->inorden($nodo->izquierda, $resultado);
        $resultado[] = $nodo->valor;
        $this->inorden($nodo->derecha, $resultado);
    }

    // Nodo - Izquierda - Derecha
    public function preorden(?Nodo $nodo, array &$resultado): void {
        if ($nodo === null) return;
        $resultado[] = $nodo->valor;
        $this->preorden($nodo->izquierda, $resultado);
        $this->preorden($nodo->derecha, $resultado);
    }

    // Izquierda - Derecha - Nodo
    public function posorden(?Nodo $nodo, array &$resultado): void {
        if ($nodo === null) return;
        $this->posorden($nodo->izquierda, $resultado);
        $this->posorden($nodo->derecha, $resultado);
        $resultado[] = $nodo->valor;
    }

    // Imprime el arbol rotado (raiz a la izquierda) con ramas tipo "arbol genealogico"
    // Se deja por si se quiere ver tambien en consola (php arbol.php en terminal)
    public function mostrar(?Nodo $nodo, string $prefijo = "", bool $esRaiz = true, bool $esIzquierda = false): void {
        if ($nodo === null) return;

        if ($nodo->derecha !== null) {
            $nuevoPrefijo = $prefijo . ($esRaiz ? "   " : ($esIzquierda ? "│   " : "    "));
            $this->mostrar($nodo->derecha, $nuevoPrefijo, false, false);
        }

        echo $prefijo;
        if (!$esRaiz) {
            echo $esIzquierda ? "└── " : "┌── ";
        }
        echo $nodo->valor . "\n";

        if ($nodo->izquierda !== null) {
            $nuevoPrefijo = $prefijo . ($esRaiz ? "   " : ($esIzquierda ? "    " : "│   "));
            $this->mostrar($nodo->izquierda, $nuevoPrefijo, false, true);
        }
    }

    // Calcula la posicion x,y de cada nodo para dibujarlo en el SVG
    // x se basa en el orden inorden (asi las ramas nunca se cruzan)
    // y se basa en la profundidad del nodo
    public function calcularPosiciones(?Nodo $nodo, int $profundidad = 0): void {
        if ($nodo === null) return;

        $this->calcularPosiciones($nodo->izquierda, $profundidad + 1);

        $nodo->x = $this->contadorX * 70 + 50;
        $nodo->y = $profundidad * 80 + 40;
        $this->contadorX++;

        $this->calcularPosiciones($nodo->derecha, $profundidad + 1);
    }

    // Calcula cuantos niveles de profundidad tiene el arbol,
    // para saber que tan alto debe ser el SVG
    public function calcularProfundidad(?Nodo $nodo): int {
        if ($nodo === null) return 0;

        $izq = $this->calcularProfundidad($nodo->izquierda);
        $der = $this->calcularProfundidad($nodo->derecha);

        return 1 + max($izq, $der);
    }
}

// Dibuja las lineas (conexiones) recursivamente
function dibujarLineas(?Nodo $nodo): string {
    if ($nodo === null) return "";

    $svg = "";

    if ($nodo->izquierda !== null) {
        $svg .= "<line x1='{$nodo->x}' y1='{$nodo->y}' x2='{$nodo->izquierda->x}' y2='{$nodo->izquierda->y}' stroke='#7cc4ff' stroke-width='2' />";
        $svg .= dibujarLineas($nodo->izquierda);
    }

    if ($nodo->derecha !== null) {
        $svg .= "<line x1='{$nodo->x}' y1='{$nodo->y}' x2='{$nodo->derecha->x}' y2='{$nodo->derecha->y}' stroke='#7cc4ff' stroke-width='2' />";
        $svg .= dibujarLineas($nodo->derecha);
    }

    return $svg;
}

// Dibuja los circulos (nodos) recursivamente
function dibujarNodos(?Nodo $nodo): string {
    if ($nodo === null) return "";

    $svg = "<circle cx='{$nodo->x}' cy='{$nodo->y}' r='22' fill='url(#colorNodo)' stroke='#fff' stroke-width='1.5' />";
    $svg .= "<text x='{$nodo->x}' y='" . ($nodo->y + 5) . "' text-anchor='middle' fill='white' font-weight='bold' font-size='14'>{$nodo->valor}</text>";

    $svg .= dibujarNodos($nodo->izquierda);
    $svg .= dibujarNodos($nodo->derecha);

    return $svg;
}

// ---- Programa principal con los numeros pedidos ----

$datos = [60, 41, 74, 16, 53, 65, 25, 46, 55, 63, 70, 42, 62, 64];

$arbol = new ArbolBinario();
foreach ($datos as $valor) {
    $arbol->insertar($valor);
}

$arbol->calcularPosiciones($arbol->raiz);

// recorridos inorden, preorden y posorden
$inorden = [];
$arbol->inorden($arbol->raiz, $inorden);

$preorden = [];
$arbol->preorden($arbol->raiz, $preorden);

$posorden = [];
$arbol->posorden($arbol->raiz, $posorden);

// Validacion: el inorden de un BST siempre debe quedar ordenado
$copia = $inorden;
sort($copia);
$valido = ($inorden === $copia);

$profundidad = $arbol->calcularProfundidad($arbol->raiz);

$anchoSvg = count($datos) * 70 + 50;
$altoSvg = $profundidad * 80 + 40;
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Arbol Binario de Busqueda</title>
<style>
    body {
        font-family: 'Segoe UI', sans-serif;
        background: #1e1e2f;
        color: #eee;
        padding: 40px;
    }
    h1 { color: #7cc4ff; }
    h2 { color: #aaa; font-weight: normal; margin-top: 40px; }
    .contenedor-svg {
        background: #262638;
        border-radius: 12px;
        padding: 20px;
        overflow-x: auto;
    }
    .recorrido {
        background: #262638;
        border-radius: 8px;
        padding: 12px 18px;
        margin: 10px 0;
        font-family: monospace;
        font-size: 15px;
    }
    .recorrido b { color: #7cc4ff; }
    .ok { color: #6fd94e; font-weight: bold; }
    .error { color: #ff6f6f; font-weight: bold; }
    footer { margin-top: 40px; color: #777; font-size: 13px; }
</style>
</head>
<body>

<h1>Arbol Binario de Busqueda</h1>
<p>Datos insertados: <?= implode(", ", $datos) ?></p>

<h2>Representacion grafica</h2>
<div class="contenedor-svg">
<svg width="<?= $anchoSvg ?>" height="<?= $altoSvg ?>">
    <defs>
        <linearGradient id="colorNodo" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" stop-color="#4e8cff" />
            <stop offset="100%" stop-color="#2b5ed6" />
        </linearGradient>
    </defs>
    <?= dibujarLineas($arbol->raiz) ?>
    <?= dibujarNodos($arbol->raiz) ?>
</svg>
</div>

<h2>Recorridos</h2>
<div class="recorrido"><b>Inorden</b> (izq-nodo-der): <?= implode(", ", $inorden) ?></div>
<div class="recorrido"><b>Preorden</b> (nodo-izq-der): <?= implode(", ", $preorden) ?></div>
<div class="recorrido"><b>Posorden</b> (izq-der-nodo): <?= implode(", ", $posorden) ?></div>

<h2>Validacion</h2>
<p>
<?php if ($valido): ?>
    <span class="ok">✓ El recorrido inorden esta ordenado, el arbol es correcto.</span>
<?php else: ?>
    <span class="error">✗ El recorrido inorden no quedo ordenado.</span>
<?php endif; ?>
</p>
</body>
</html>
