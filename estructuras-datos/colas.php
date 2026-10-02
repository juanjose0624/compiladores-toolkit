<?php
// Cola (Queue) - FIFO
// Genera una pagina HTML con la representacion visual

class Cola {
    private array $elementos = [];

    public function enqueue(int $valor): void {
        array_push($this->elementos, $valor);
    }

    public function dequeue(): ?int {
        if ($this->estaVacia()) return null;
        return array_shift($this->elementos);
    }

    public function frente(): ?int {
        if ($this->estaVacia()) return null;
        return $this->elementos[0];
    }

    public function estaVacia(): bool {
        return count($this->elementos) === 0;
    }

    public function aArreglo(): array {
        return $this->elementos;
    }
}

// ---- Programa principal ----

$cola = new Cola();
$cola->enqueue(1);
$cola->enqueue(2);
$cola->enqueue(3);

$antes = $cola->aArreglo();
$sacado = $cola->dequeue();
$despues = $cola->aArreglo();

function dibujarCola(array $valores): string {
    if (count($valores) === 0) {
        return "<p class='vacio'>Cola vacia</p>";
    }

    $html = "<div class='cola'>";
    $html .= "<div class='etiqueta'>salida &larr;</div>";
    foreach ($valores as $valor) {
        $html .= "<div class='nodo'>$valor</div>";
    }
    $html .= "<div class='etiqueta'>&larr; entrada</div>";
    $html .= "</div>";

    return $html;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Cola (Queue)</title>
<style>
    body {
        font-family: 'Segoe UI', sans-serif;
        background: #1e1e2f;
        color: #eee;
        padding: 40px;
    }
    h1 { color: #ff7ccb; }
    h2 { color: #aaa; font-weight: normal; margin-top: 40px; }
    .cola {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        margin: 20px 0;
    }
    .nodo {
        background: linear-gradient(135deg, #ff6fbf, #d62b93);
        color: white;
        padding: 15px 20px;
        margin-right: 10px;
        border-radius: 10px;
        font-weight: bold;
        box-shadow: 0 4px 10px rgba(0,0,0,0.3);
    }
    .etiqueta {
        color: #ff7ccb;
        font-size: 14px;
        margin-right: 10px;
    }
    .vacio { color: #888; font-style: italic; }
</style>
</head>
<body>

<h1>Cola (Queue) - FIFO</h1>

<h2>Cola despues de enqueue(1), enqueue(2), enqueue(3)</h2>
<?= dibujarCola($antes) ?>

<h2>Despues de hacer dequeue() (se saco el <?= $sacado ?>)</h2>
<?= dibujarCola($despues) ?>
</body>
</html>