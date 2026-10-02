<?php
// Lista enlazada simple
// Genera una pagina HTML con la representacion visual de la lista

class Nodo {
    public int $valor;
    public ?Nodo $siguiente = null;

    public function __construct(int $valor) {
        $this->valor = $valor;
    }
}

class ListaEnlazada {
    public ?Nodo $cabeza = null;

    public function agregar(int $valor): void {
        $nuevo = new Nodo($valor);

        if ($this->cabeza === null) {
            $this->cabeza = $nuevo;
            return;
        }

        $actual = $this->cabeza;
        while ($actual->siguiente !== null) {
            $actual = $actual->siguiente;
        }
        $actual->siguiente = $nuevo;
    }

    public function eliminar(int $valor): void {
        if ($this->cabeza === null) return;

        if ($this->cabeza->valor === $valor) {
            $this->cabeza = $this->cabeza->siguiente;
            return;
        }

        $actual = $this->cabeza;
        while ($actual->siguiente !== null && $actual->siguiente->valor !== $valor) {
            $actual = $actual->siguiente;
        }

        if ($actual->siguiente !== null) {
            $actual->siguiente = $actual->siguiente->siguiente;
        }
    }

    // Devuelve los valores en un arreglo, para poder dibujarlos
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

$lista = new ListaEnlazada();
$lista->agregar(10);
$lista->agregar(20);
$lista->agregar(30);
$lista->agregar(40);

$antes = $lista->aArreglo();

$lista->eliminar(20);

$despues = $lista->aArreglo();

// Funcion para dibujar la lista como cajas con flechas
function dibujarLista(array $valores): string {
    if (count($valores) === 0) {
        return "<p class='vacio'>Lista vacia</p>";
    }

    $html = "<div class='lista'>";
    foreach ($valores as $i => $valor) {
        $html .= "<div class='nodo'>$valor</div>";
        if ($i < count($valores) - 1) {
            $html .= "<div class='flecha'>&rarr;</div>";
        }
    }
    $html .= "<div class='flecha'>&rarr;</div><div class='nulo'>NULL</div>";
    $html .= "</div>";

    return $html;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Lista Enlazada Simple</title>
<style>
    body {
        font-family: 'Segoe UI', sans-serif;
        background: #1e1e2f;
        color: #eee;
        padding: 40px;
    }
    h1 { color: #7cc4ff; }
    h2 { color: #aaa; font-weight: normal; margin-top: 40px; }
    .lista {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        margin: 20px 0;
    }
    .nodo {
        background: linear-gradient(135deg, #4e8cff, #2b5ed6);
        color: white;
        padding: 15px 20px;
        border-radius: 10px;
        font-weight: bold;
        box-shadow: 0 4px 10px rgba(0,0,0,0.3);
    }
    .flecha {
        font-size: 24px;
        margin: 0 10px;
        color: #7cc4ff;
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

<h1>Lista Enlazada Simple</h1>

<h2>Lista original (10, 20, 30, 40)</h2>
<?= dibujarLista($antes) ?>

<h2>Despues de eliminar el 20</h2>
<?= dibujarLista($despues) ?>
</body>
</html>