<?php
/**
 * Menú común del proyecto. Detecta en qué sección está el usuario y muestra
 * las demás para saltar directo, sin volver atrás. Las secciones que tienen
 * 'hijos' (como Estructuras) se muestran como un menú desplegable.
 *
 * Uso en cada página PHP:
 *   require_once __DIR__ . '/../includes/menu.php';  // ajusta los ../ a la carpeta de la página
 *   <head>  ...  <?php tk_menu_head(); ?>
 *   <body>       <?php tk_menu(); ?>   // debe ser lo primero dentro de <body>
 */

// Secciones del proyecto. Ajusta 'url' y 'carpeta' a tus carpetas reales.
// 'url' va desde la raíz del proyecto. 'hijos' (opcional) convierte la sección en un desplegable
// con sus páginas: archivo => título (los enlaces se arman con 'carpeta', no hace falta 'url').
function tk_secciones(): array
{
    return [
        'inicio'      => ['titulo' => 'Inicio', 'url' => '/', 'carpeta' => ''],
        'notaciones'  => ['titulo' => 'Notaciones', 'url' => '/notaciones/controller.php', 'carpeta' => 'notaciones'],
        'automatas'   => ['titulo' => 'Autómatas', 'url' => '/automatas/automatas.php', 'carpeta' => 'automatas'],
        'estructuras' => [
            'titulo'  => 'Estructuras',
            'carpeta' => 'estructuras-datos',
            'hijos'   => [
                'listas.php'        => 'Listas',
                'listas_dobles.php' => 'Listas dobles',
                'pilas.php'         => 'Pilas',
                'colas.php'         => 'Colas',
                'arbol.php'         => 'Árbol',
            ],
        ],
        'calculadora' => ['titulo' => 'Calculadora', 'url' => '/calculadora/calculadora.php', 'carpeta' => 'calculadora'],
    ];
}

// Ruta web de la raíz del proyecto, calculada sola (ej. "/compiladores-toolkit").
function tk_base(): string
{
    static $base = null;
    if ($base === null) {
        $raiz = rtrim(str_replace('\\', '/', (string) realpath(dirname(__DIR__))), '/');
        $web  = rtrim(str_replace('\\', '/', (string) realpath($_SERVER['DOCUMENT_ROOT'] ?? '')), '/');
        $base = ($web !== '' && stripos($raiz, $web) === 0) ? substr($raiz, strlen($web)) : '';
    }
    return $base;
}

// Calcula la sección actual mirando la primera carpeta de la dirección.
function tk_estado(): array
{
    $secciones = tk_secciones();
    $ruta      = rawurldecode(parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?: '');
    $relativa  = ltrim(substr($ruta, strlen(tk_base())), '/');
    $primero   = explode('/', $relativa)[0];

    $actual = 'inicio';
    foreach ($secciones as $clave => $s) {
        if ($s['carpeta'] !== '' && strcasecmp($s['carpeta'], $primero) === 0) {
            $actual = $clave;
            break;
        }
    }
    return [$secciones, $actual, basename($ruta)];
}

// Etiquetas para el <head>: hoja de estilos y script del menú.
function tk_menu_head(): void
{
    $base = htmlspecialchars(tk_base(), ENT_QUOTES, 'UTF-8');
    echo '<link rel="stylesheet" href="' . $base . '/css/menu.css">' . "\n";
    echo '<script src="' . $base . '/js/menu.js" defer></script>' . "\n";
}

// El menú en sí.
function tk_menu(): void
{
    [$secciones, $actual, $archivo] = tk_estado();
    $base = tk_base();
    $h    = fn(string $s): string => htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
?>
<header class="tk-menu">
  <div class="tk-barra">
    <a class="tk-marca" href="<?= $h($base) ?>/"><b>f(x)</b><span>Compiladores Toolkit</span></a>
    <nav class="tk-nav" aria-label="Secciones">
      <ul>
        <?php foreach ($secciones as $clave => $s): $esActual = ($clave === $actual); ?>
          <?php if (!empty($s['hijos'])): ?>
            <li class="tk-tiene-sub">
              <button type="button" class="tk-boton-sub" aria-expanded="false" aria-controls="tk-sub-<?= $h($clave) ?>"<?= $esActual ? ' aria-current="page"' : '' ?>>
                <?= $h($s['titulo']) ?><span class="tk-caret" aria-hidden="true"></span>
              </button>
              <ul class="tk-desplegable" id="tk-sub-<?= $h($clave) ?>">
                <?php foreach ($s['hijos'] as $archivoHijo => $titulo): ?>
                  <li><a href="<?= $h($base . '/' . $s['carpeta'] . '/' . $archivoHijo) ?>"<?= ($esActual && $archivo === $archivoHijo) ? ' aria-current="page"' : '' ?>><?= $h($titulo) ?></a></li>
                <?php endforeach; ?>
              </ul>
            </li>
          <?php else: ?>
            <li><a href="<?= $h($base . $s['url']) ?>"<?= $esActual ? ' aria-current="page"' : '' ?>><?= $h($s['titulo']) ?></a></li>
          <?php endif; ?>
        <?php endforeach; ?>
      </ul>
    </nav>
  </div>
</header>
<?php
}