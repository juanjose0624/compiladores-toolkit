<?php
// Lista doblemente enlazada
// Genera una pagina HTML con la representacion visual

class Nodo {
    public int $valor;
    public ?Nodo $siguiente = null;
    public ?Nodo $anterior = null;

    public function __construct(int $valor) {
        $this->valor = $valor;
    }
}

class ListaDoble {
    public ?Nodo $cabeza = null;
    public ?Nodo $cola = null;

    public function agregar(int $valor): void {
        $nuevo = new Nodo($valor);

        if ($this->cabeza === null) {
            $this->cabeza = $nuevo;
            $this->cola = $nuevo;
            return;
        }

        $nuevo->anterior = $this->cola;
        $this->cola->siguiente = $nuevo;
        $this->cola = $nuevo;
    }

    public function eliminar(int $valor): void {
        $actual = $this->cabeza;

        while ($actual !== null && $actual->valor !== $valor) {
            $actual = $actual->siguiente;
        }

        if ($actual === null) return;

        if ($actual->anterior !== null) {
            $actual->anterior->siguiente = $actual->siguiente;
        } else {
            $this->cabeza = $actual->siguiente;
        }

        if ($actual->siguiente !== null) {
            $actual->siguiente->anterior = $actual->anterior;
        } else {
            $this->cola = $actual->anterior;
        }
    }

    public function aArreglo(): array {
        $valores = [];
        $actual = $this->cabeza;

        while ($actual !== null) {
            $valores[] = $actual->valor;
            $actual = $actual->siguiente;
        }

        return $valores;
    }
}

// ---- Programa principal ----

$lista = new ListaDoble();
$lista->agregar(10);
$lista->agregar(20);
$lista->agregar(30);
$lista->agregar(40);

$antes = $lista->aArreglo();

$lista->eliminar(20);

$despues = $lista->aArreglo();

function dibujarListaDoble(array $valores): string {
    if (count($valores) === 0) {
        return "<p class='vacio'>Lista vacia</p>";
    }

    $html = "<div class='lista'>";
    $html .= "<div class='nulo'>NULL</div><div class='flecha'>&harr;</div>";

    foreach ($valores as $i => $valor) {
        $html .= "<div class='nodo'>$valor</div>";
        $html .= "<div class='flecha'>&harr;</div>";
    }

    $html .= "<div class='nulo'>NULL</div>";
    $html .= "</div>";

    return $html;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Lista Doblemente Enlazada</title>
<style>
    body {
        font-family: 'Segoe UI', sans-serif;
        background: #1e1e2f;
        color: #eee;
        padding: 40px;
    }
    h1 { color: #ffb37c; }
    h2 { color: #aaa; font-weight: normal; margin-top: 40px; }
    .lista {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        margin: 20px 0;
    }
    .nodo {
        background: linear-gradient(135deg, #ff9d4e, #d6702b);
        color: white;
        padding: 15px 20px;
        border-radius: 10px;
        font-weight: bold;
        box-shadow: 0 4px 10px rgba(0,0,0,0.3);
    }
    .flecha {
        font-size: 22px;
        margin: 0 8px;
        color: #ffb37c;
    }
    .nulo {
        background: #3a3a4d;
        color: #999;
        padding: 15px 20px;
        border-radius: 10px;
        font-style: italic;
    }
    .vacio { color: #888; font-style: italic; }
</style>
</head>
<body>

<h1>Lista Doblemente Enlazada</h1>

<h2>Lista original (10, 20, 30, 40)</h2>
<?= dibujarListaDoble($antes) ?>

<h2>Despues de eliminar el 20</h2>
<?= dibujarListaDoble($despues) ?>
</body>
</html>