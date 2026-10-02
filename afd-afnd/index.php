<?php
session_start();

require_once __DIR__ . '/Automata.php';
require_once __DIR__ . '/AFD.php';
require_once __DIR__ . '/AFN.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tipoSeleccionado = $_POST['tipo'] ?? 'afd';
    $cadenaInput = trim($_POST['cadena'] ?? '');

    if ($cadenaInput === '' || !preg_match('/^[01]+$/', $cadenaInput)) {
        $_SESSION['errorInput'] = 'La cadena solo puede contener los símbolos 0 y 1.';
        $_SESSION['resultado'] = null;
    } else {
        /** @var Automata $automata */
        if ($tipoSeleccionado === 'afn') {
            $automata = new AFN();
        } else {
            $automata = new AFD();
        }
        $_SESSION['resultado'] = $automata->ejecutar($cadenaInput);
        $_SESSION['errorInput'] = null;
    }

    $_SESSION['tipoSeleccionado'] = $tipoSeleccionado;
    $_SESSION['cadenaInput'] = $cadenaInput;

    // Redirige con GET para que al recargar no se reenvíe el formulario
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

// En una petición GET normal, recuperamos lo último guardado en sesión (si existe)
$resultado = $_SESSION['resultado'] ?? null;
$errorInput = $_SESSION['errorInput'] ?? null;
$tipoSeleccionado = $_SESSION['tipoSeleccionado'] ?? 'afd';
$cadenaInput = $_SESSION['cadenaInput'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Simulador de Autómatas Finitos</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="style.css">
</head>
<body>

<div class="app-header">
  <div class="container">
    <h1>Simulador de Autómatas Finitos</h1>
    <p>Escribe una cadena binaria y observa cómo un AFD y un AFN procesan cada símbolo de forma distinta.</p>
  </div>
</div>

<div class="container py-4">
  <div class="row g-4">

    <div class="col-lg-4">
      <div class="panel">
        <h2>Configuración</h2>
        <form method="POST">
          <div class="mb-3">
            <label class="form-label d-block mb-2">Tipo de autómata</label>
            <div class="tipo-toggle">
              <input type="radio" name="tipo" id="tipo-afd" value="afd" <?= $tipoSeleccionado === 'afd' ? 'checked' : '' ?>>
              <label for="tipo-afd">AFD</label>
              <input type="radio" name="tipo" id="tipo-afn" value="afn" <?= $tipoSeleccionado === 'afn' ? 'checked' : '' ?>>
              <label for="tipo-afn">AFN</label>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label" for="cadena">Cadena binaria</label>
            <input
              type="text"
              class="form-control"
              id="cadena"
              name="cadena"
              placeholder="p. ej. 0101"
              value="<?= htmlspecialchars($cadenaInput) ?>"
              autocomplete="off"
            >
          </div>

          <?php if ($errorInput): ?>
            <div class="text-danger small mb-3"><?= htmlspecialchars($errorInput) ?></div>
          <?php endif; ?>

          <button type="submit" class="btn btn-run w-100">Ejecutar</button>
        </form>

        <div class="automaton-desc">
          <?php if ($tipoSeleccionado === 'afn'): ?>
            <strong class="text-body">AFN</strong> — acepta cadenas que <em>contienen</em> la subcadena <span class="mono">01</span> en algún punto. Desde <span class="mono">q0</span> con un <span class="mono">0</span> el autómata se ramifica a dos estados posibles a la vez.
          <?php else: ?>
            <strong class="text-body">AFD</strong> — acepta cadenas que <em>terminan</em> en <span class="mono">01</span>. Cada estado tiene una única transición definida por símbolo.
          <?php endif; ?>
        </div>
      </div>
    </div>

    <div class="col-lg-8">
      <div class="panel">
        <h2>Traza de ejecución</h2>

        <?php if (!$resultado): ?>
          <div class="empty-state">Ejecuta una cadena para ver la traza paso a paso.</div>
        <?php else: ?>

          <div class="result-banner <?= $resultado['aceptada'] ? 'result-accept' : 'result-reject' ?>">
            <span>
              <?= $resultado['tipo'] ?> · cadena
              "<span class="mono"><?= htmlspecialchars($resultado['cadena']) ?></span>"
            </span>
            <span><?= $resultado['aceptada'] ? 'ACEPTADA' : 'RECHAZADA' ?></span>
          </div>

          <div class="trace-log">
            <?php if ($resultado['tipo'] === 'AFD'): ?>
              <?php foreach ($resultado['traza'] as $i => $paso): ?>
                <div class="trace-line">
                  <span class="trace-index">#<?= $i + 1 ?></span>
                  <span class="trace-state"><?= $paso['desde'] ?></span>
                  <span class="trace-symbol"><?= htmlspecialchars($paso['simbolo']) ?></span>
                  <span class="trace-arrow">&rarr;</span>
                  <span class="trace-state">
                    <?= $paso['hacia'] ?? '<span class="text-danger">' . htmlspecialchars($paso['error']) . '</span>' ?>
                  </span>
                </div>
              <?php endforeach; ?>
              <div class="trace-line" style="border-top: 1px solid var(--panel-border); margin-top: 0.5rem; padding-top: 0.6rem;">
                <span class="trace-index"></span>
                <span style="color: var(--text); font-weight: 600;">Estado final:</span>
                <span class="trace-state" style="color: var(--accent); font-weight: 600;"><?= $resultado['estadoFinal'] ?></span>
              </div>

            <?php else: ?>
              <?php foreach ($resultado['traza'] as $i => $paso): ?>
                <div class="trace-line">
                  <span class="trace-index">#<?= $i + 1 ?></span>
                  <span class="trace-state">{<?= implode(', ', $paso['desde']) ?>}</span>
                  <span class="trace-symbol"><?= htmlspecialchars($paso['simbolo']) ?></span>
                  <span class="trace-arrow">&rarr;</span>
                  <span class="trace-state">
                    <?= !empty($paso['hacia']) ? '{' . implode(', ', $paso['hacia']) . '}' : '<span class="text-danger">&empty; (rama muerta)</span>' ?>
                  </span>
                </div>
              <?php endforeach; ?>
              <div class="trace-line" style="border-top: 1px solid var(--panel-border); margin-top: 0.5rem; padding-top: 0.6rem;">
                <span class="trace-index"></span>
                <span style="color: var(--text); font-weight: 600;">Estados finales:</span>
                <span class="trace-state" style="color: var(--accent); font-weight: 600;">{<?= implode(', ', $resultado['estadosFinales']) ?>}</span>
              </div>
            <?php endif; ?>
          </div>

        <?php endif; ?>
      </div>
    </div>

  </div>
</div>

<footer>
  <h6>
    Desarrollado por: <br>
    Juan José Sepúlveda Álvarez · Dayana Rosario · Sebastián Sierra Vélez
  </h6>
</footer>

</body>
</html>
