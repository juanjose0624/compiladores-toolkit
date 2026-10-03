<?php require_once __DIR__ . '../includes/menu.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Compiladores Toolkit</title>
  <meta name="description" content="Autómatas, estructuras de datos, notaciones y calculadora científica en una sola aplicación web hecha en PHP, HTML, CSS y JavaScript.">
<link rel="stylesheet" href="css/index.css">
  <?php tk_menu_head(); ?>
</head>
<body> 
    <?php tk_menu(); ?>
  <div class="envoltura">
    <div class="barra">
      <span>Compiladores Toolkit</span>
      <a href="https://github.com/juanjose0624/compiladores-toolkit">GitHub</a>
    </div>

    <header class="hero">
      <h1>Compiladores Toolkit</h1>
      <p class="intro">Herramientas web de la materia de Compiladores reunidas en un solo proyecto: simula autómatas, recorre estructuras de datos, convierte expresiones entre notaciones y calcula.</p>
      <div class="formas" role="img" aria-label="La expresión A + B * C escrita en notación infija, posfija y prefija">
        <div class="forma"><span>infija</span><code>A <span class="op">+</span> B <span class="op">*</span> C</code></div>
        <div class="forma"><span>posfija</span><code>A B C <span class="op">*</span> <span class="op">+</span></code></div>
        <div class="forma"><span>prefija</span><code><span class="op">+</span> A <span class="op">*</span> B C</code></div>
      </div>
    </header>

    <main>
      <h2>Qué puedes hacer</h2>
      <div class="cuadricula">
        <section class="modulo m-notaciones">
          <h3>Convertir notaciones</h3>
          <p>Pasa una expresión entre infija, prefija y posfija. Acepta números, variables y paréntesis, y avisa cuando la expresión está mal formada.</p>
          <div class="visual glifos" aria-hidden="true"><span>+</span><span>-</span><span>*</span><span>/</span><span>%</span><span>^</span></div>
          <ul class="enlaces"><li><a href="notaciones/controller.php">Abrir el conversor</a></li></ul>
        </section>

        <section class="modulo m-automatas">
          <h3>Simular autómatas</h3>
          <p>Elige un AFD o un AFN, escribe una cadena binaria y mira si la acepta, con la traza paso a paso. El AFD reconoce las cadenas que terminan en <code>01</code> y el AFN, las que contienen <code>01</code>.</p>
          <svg class="visual automata" viewBox="0 0 320 120" aria-hidden="true">
            <path d="M8 60 H34"/><circle cx="62" cy="60" r="26"/><text x="51" y="65">q0</text>
            <path d="M86 48 C 120 20, 200 20, 234 48"/><text x="156" y="22">a</text>
            <path d="M234 72 C 200 100, 120 100, 86 72"/><text x="156" y="108">b</text>
            <circle cx="260" cy="60" r="26"/><circle cx="260" cy="60" r="20"/><text x="249" y="65">q1</text>
          </svg>
          <ul class="enlaces"><li><a href="automatas/automatas.php">Abrir el simulador</a></li></ul>
        </section>

        <section class="modulo m-estructuras">
          <h3>Recorrer estructuras de datos</h3>
          <p>Listas simples y dobles, pilas, colas y árboles, con las operaciones fundamentales que usa un compilador: evaluar expresiones, leer tokens y representar árboles sintácticos.</p>
          <div class="visual lista" aria-hidden="true"><span class="nodo">12</span>&rarr;<span class="nodo">7</span>&rarr;<span class="nodo">3</span>&rarr;<span class="nodo nulo">nulo</span></div>
          <ul class="enlaces">
            <li><a href="estructuras-datos/listas.php">Listas</a></li>
            <li><a href="estructuras-datos/listas_dobles.php">Listas dobles</a></li>
            <li><a href="estructuras-datos/pilas.php">Pilas</a></li>
            <li><a href="estructuras-datos/colas.php">Colas</a></li>
            <li><a href="estructuras-datos/arbol.php">Árbol</a></li>
          </ul>
        </section>

        <section class="modulo m-calculadora">
          <h3>Calcular</h3>
          <p>Calculadora científica con trigonometría, logaritmos y factorial. Convierte entre decimal, binario, octal y hexadecimal, con atajos de teclado y tema claro u oscuro.</p>
          <div class="visual teclas" aria-hidden="true"><span>7</span><span>8</span><span>9</span><span class="op">÷</span><span>4</span><span>5</span><span>6</span><span class="op">×</span><span>1</span><span>2</span><span>3</span><span class="op">-</span></div>
          <ul class="enlaces"><li><a href="calculadora/calculadora.php">Abrir la calculadora</a></li></ul>
        </section>
      </div>

      <h2>Con qué está hecha</h2>
      <div class="hecha">
        <div><strong>PHP</strong><p>La lógica: autómatas, estructuras de datos y conversor de notaciones.</p></div>
        <div><strong>HTML y CSS</strong><p>La interfaz de todas las secciones.</p></div>
        <div><strong>JavaScript</strong><p>La calculadora, que funciona completa en el navegador.</p></div>
        <div><strong>Librerías</strong><p>Math.js y SweetAlert2, en la calculadora.</p></div>
      </div>

      <h2>Cómo ejecutarla</h2>
      <p class="req">Necesitas PHP 7.4 o superior y un servidor local como XAMPP.</p>
      <div class="pasos">
        <div><span class="n">1</span><h3>Copia el proyecto</h3><p>Pon la carpeta dentro de <code>htdocs</code>.</p></div>
        <div><span class="n">2</span><h3>Inicia Apache</h3><p>Hazlo desde el panel de control de XAMPP.</p></div>
        <div><span class="n">3</span><h3>Abre el navegador</h3><p>Entra a <code>http://localhost/compiladores-toolkit/</code></p></div>
      </div>
    </main>
  </div>

  <footer>
    <div class="envoltura">
      Hecho por Juan José Sepúlveda Álvarez, estudiante de Ingeniería Informática. Licencia MIT.
    </div>
  </footer>
</body>
</html>