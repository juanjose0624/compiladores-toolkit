<?php
/**
 * Vista: solo presentación.
 *
 * @param string      $expr  Expresión escrita por el usuario
 * @param string      $modo  Notación de entrada: infija | prefija | posfija
 * @param array|null  $res   Resultado con las claves infija, prefija, posfija
 * @param string|null $error Mensaje de error de validación
 * @param array       $ejemplos Lista de ['modo' => ..., 'expr' => ..., 'res' => [infija, prefija, posfija]]
 */
function renderVista(string $expr, string $modo, ?array $res, ?string $error, array $ejemplos = []): void
{
    $h = fn(string $s): string => htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Conversor de notaciones</title>
<link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 64 64'%3E%3Crect width='64' height='64' rx='14' fill='%234f46e5'/%3E%3Ctext x='32' y='42' font-family='Consolas,monospace' font-size='24' font-weight='700' text-anchor='middle' fill='white'%3Ef(x)%3C/text%3E%3C/svg%3E">
<link rel="stylesheet" href="../css/notaciones.css">
  <?php tk_menu_head(); ?>
</head>
<body>
    <?php tk_menu(); ?>
<main>
  <h1>Conversor de notaciones</h1>

  <form method="post" aria-label="Convertir una expresión">
    <label>Notación de entrada
      <select name="modo">
        <?php foreach (['infija', 'prefija', 'posfija'] as $m): ?>
          <option value="<?= $m ?>" <?= $modo === $m ? 'selected' : '' ?>><?= ucfirst($m) ?></option>
        <?php endforeach; ?>
      </select>
    </label>
    <label>Expresión
      <input type="text" name="expr" value="<?= $h($expr) ?>" placeholder="Ej: (A + B) * C - D / E" autocomplete="off" required>
    </label>
    <button type="submit">Convertir</button>
  </form>

  <section aria-live="polite">
    <?php if ($error): ?>
      <p class="error" role="alert"><?= $h($error) ?></p>
    <?php elseif ($res): ?>
      <table>
        <tr><th scope="row">Infija</th><td><code><?= $h($res['infija']) ?></code></td></tr>
        <tr><th scope="row">Prefija</th><td><code><?= $h($res['prefija']) ?></code></td></tr>
        <tr><th scope="row">Posfija</th><td><code><?= $h($res['posfija']) ?></code></td></tr>
      </table>
    <?php endif; ?>
  </section>

  <?php if ($ejemplos): ?>
    <section aria-labelledby="titulo-ejemplos">
      <h2 id="titulo-ejemplos">Ejemplos para probar</h2>
      <p class="nota">En prefija y posfija los operandos van separados por espacio.</p>
      <table class="ejemplos">
        <thead>
          <tr>
            <th scope="col">Entrada</th>
            <th scope="col">Expresión</th>
            <th scope="col">Infija</th>
            <th scope="col">Prefija</th>
            <th scope="col">Posfija</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($ejemplos as $ej): ?>
            <tr>
              <td><?= $h(ucfirst($ej['modo'])) ?></td>
              <td><code><?= $h($ej['expr']) ?></code></td>
              <td><code><?= $h($ej['res']['infija']) ?></code></td>
              <td><code><?= $h($ej['res']['prefija']) ?></code></td>
              <td><code><?= $h($ej['res']['posfija']) ?></code></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </section>
  <?php endif; ?>
</main>

<footer>
  <strong>Compiladores</strong>
  <span>Juan José Sepúlveda Álvarez</span>
</footer>
</body>
</html>
<?php
}