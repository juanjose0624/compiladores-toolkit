// Menú común: abre y cierra los desplegables y pule detalles.
// Sin JavaScript el menú sigue funcionando (el desplegable se abre al enfocar el botón).
(function () {
  const menu = document.querySelector('.tk-menu');
  if (!menu) return;
  menu.classList.add('tk-js');

  // Sombra bajo el menú cuando la página se ha desplazado.
  const marcarSombra = () => menu.classList.toggle('tk-sombra', window.scrollY > 4);
  marcarSombra();
  window.addEventListener('scroll', marcarSombra, { passive: true });

  // En pantallas angostas la barra se desplaza de lado: centra el elemento activo.
  const nav = menu.querySelector('.tk-nav');
  const activo = nav && nav.querySelector(':scope > ul > li > [aria-current]');
  if (activo) nav.scrollLeft = activo.offsetLeft - (nav.clientWidth - activo.offsetWidth) / 2;

  // Desplegables (Estructuras).
  const grupos = [...menu.querySelectorAll('.tk-tiene-sub')];
  const botonDe = (g) => g.querySelector('.tk-boton-sub');
  const cerrar = (g) => { g.classList.remove('tk-abierto'); botonDe(g).setAttribute('aria-expanded', 'false'); };
  const cerrarTodos = (excepto) => grupos.forEach((g) => { if (g !== excepto) cerrar(g); });

  grupos.forEach((g) => {
    botonDe(g).addEventListener('click', () => {
      const abrir = !g.classList.contains('tk-abierto');
      cerrarTodos(g);
      g.classList.toggle('tk-abierto', abrir);
      botonDe(g).setAttribute('aria-expanded', String(abrir));
    });
    // Al salir con Tab del desplegable, se cierra.
    g.addEventListener('focusout', (e) => { if (!g.contains(e.relatedTarget)) cerrar(g); });
  });

  // Clic fuera del menú: cierra.
  document.addEventListener('click', (e) => { if (!menu.contains(e.target)) cerrarTodos(); });

  // Escape: cierra y devuelve el foco al botón.
  document.addEventListener('keydown', (e) => {
    if (e.key !== 'Escape') return;
    const abierto = grupos.find((g) => g.classList.contains('tk-abierto'));
    if (abierto) { cerrar(abierto); botonDe(abierto).focus(); }
  });
})();