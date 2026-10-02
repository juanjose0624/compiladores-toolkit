<?php
// Pila (Stack) - LIFO
// Genera una pagina HTML con la representacion visual

class Pila {
    private array $elementos = [];

    public function push(int $valor): void {
        array_push($this->elementos, $valor);
    }

    public function pop(): ?int {
        if ($this->estaVacia()) return null;
        return array_pop($this->elementos);
    }

    public function peek(): ?int {
        if ($this->estaVacia()) return null;
        return end($this->elementos);
    }

    public function estaVacia(): bool {
        return count($this->elementos) === 0;
    }

    public function aArreglo(): array {
        return $this->elementos;
    }
}

// ---- Programa principal ----

$pila = new Pila();
$pila->push(1);
$pila->push(2);
$pila->push(3);

$antes = $pila->aArreglo();
$sacado = $pila->pop();
$despues = $pila->aArreglo();

// Dibuja la pila de arriba hacia abajo (el ultimo push queda arriba)
function dibujarPila(array $valores): string {
    if (count($valores) === 0) {
        return "<p class='vacio'>Pila vacia</p>";
    }

    $invertido = array_reverse($valores);

    $html = "<div class='pila'>";
    $html .= "<div class='etiqueta-tope'>&uarr; tope</div>";
    foreach ($invertido as $valor) {
        $html .= "<div class='nodo'>$valor</div>";
    }
    $html .= "<div class='base'>base</div>";
    $html .= "</div>";

    return $html;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Pila (Stack)</title>
<style>
    body {
        font-family: 'Segoe UI', sans-serif;
        background: #1e1e2f;
        color: #eee;
        padding: 40px;
    }
    h1 { color: #9dff7c; }
    h2 { color: #aaa; font-weight: normal; margin-top: 40px; }
    .pila {
        display: flex;
        flex-direction: column;
        align-items: center;
        width: 160px;
        margin: 20px 0;
    }
    .nodo {
        background: linear-gradient(135deg, #6fd94e, #3a9b28);
        color: white;
        padding: 15px 20px;
        margin-bottom: 6px;
        border-radius: 8px;
        font-weight: bold;
        width: 100%;
        text-align: center;
        box-shadow: 0 4px 10px rgba(0,0,0,0.3);
    }
    .etiqueta-tope {
        color: #9dff7c;
        margin-bottom: 8px;
        font-size: 14px;
    }
    .base {
        margin-top: 6px;
        border-top: 3px solid #666;
        width: 100%;
        text-align: center;
        color: #888;
        padding-top: 6px;
        font-size: 13px;
    }
    .vacio { color: #888; font-style: italic; }
    .info { color: #ccc; margin-top: 10px; }
</style>
</head>
<body>

<h1>Pila (Stack) - LIFO</h1>

<h2>Pila despues de push(1), push(2), push(3)</h2>
<?= dibujarPila($antes) ?>

<h2>Despues de hacer pop() (se saco el <?= $sacado ?>)</h2>
<?= dibujarPila($despues) ?>
</body>
</html>