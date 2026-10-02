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
<title>Conversor de notaciones</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>
<h1>Conversor de notaciones</h1>
<form method="post">
  <label>Notación de entrada:
    <select name="modo">
      <?php foreach (['infija', 'prefija', 'posfija'] as $m): ?>
        <option value="<?= $m ?>" <?= $modo === $m ? 'selected' : '' ?>><?= ucfirst($m) ?></option>
      <?php endforeach; ?>
    </select>
  </label>
  <input type="text" name="expr" value="<?= $h($expr) ?>" placeholder="Ej: (A + B) * C - D / E" required>
  <button type="submit">Convertir</button>
</form>

<?php if ($error): ?>
  <p class="error"><?= $h($error) ?></p>
<?php elseif ($res): ?>
  <table>
    <tr><th>Infija</th><td><code><?= $h($res['infija']) ?></code></td></tr>
    <tr><th>Prefija</th><td><code><?= $h($res['prefija']) ?></code></td></tr>
    <tr><th>Posfija</th><td><code><?= $h($res['posfija']) ?></code></td></tr>
  </table>
<?php endif; ?>
<?php if ($ejemplos): ?>
  <h2>Ejemplos para probar</h2>
  <p class="nota">
    En prefija y posfija los operandos van separados por espacio.
  </p>
  <table class="ejemplos">
    <thead>
      <tr><th>Entrada</th><th>Expresión</th><th>Infija</th><th>Prefija</th><th>Posfija</th></tr>
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
<?php endif; ?>

<footer>
  <strong>Compiladores</strong>
  <span>Juan José Sepúlveda Álvarez · Sebastian Sierra Velez · Dayana Rosario</span>
</footer>
</body>
</html>
<?php
}