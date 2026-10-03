# Compiladores Toolkit

Colección de herramientas web hechas en **PHP**, **HTML**, **CSS** y **JavaScript** para la materia de Compiladores: un simulador de autómatas finitos, estructuras de datos aplicadas a compiladores, un conversor de notaciones (infija, prefija y posfija) y una calculadora científica.

---

## Módulos

### Autómatas AFD y AFN (`afd-afnd/`)

Simulador web que permite elegir entre un **Autómata Finito Determinístico (AFD)** o uno **No Determinístico (AFN)**, ingresar una cadena binaria y ver si es aceptada, junto con la traza paso a paso de las transiciones.

- **AFD:** reconoce cadenas binarias que **terminan en `01`**. Cada estado tiene exactamente una transición por símbolo. Estados `q0`, `q1`, `q2`; inicial `q0`; de aceptación `q2`.
- **AFN:** reconoce cadenas binarias que **contienen la subcadena `01`**. Un par (estado, símbolo) puede llevar a varios estados, o a ninguno. Mismos estados inicial y de aceptación.

Ambos implementan la interfaz común `Automata` con el método `ejecutar(string $cadena): array`, así que `index.php` los trata de forma polimórfica. Cada ejecución devuelve el tipo de autómata, la cadena evaluada, la traza de transiciones, los estados finales alcanzados y si la cadena fue aceptada.

```
afd-afnd/
├── Automata.php   # Interfaz común
├── AFD.php        # Autómata determinístico
├── AFN.php        # Autómata no determinístico
├── index.php      # Interfaz web
└── style.css
```

### Estructuras de datos (`estructuras-datos/`)

Implementación en PHP de estructuras lineales y no lineales, enfocadas en las operaciones fundamentales del desarrollo de compiladores.

| Archivo | Estructura | Uso en compiladores |
|---|---|---|
| `listas.php` | Lista simplemente enlazada | Manipulación dinámica de elementos |
| `listas_dobles.php` | Lista doblemente enlazada | Recorridos en ambos sentidos |
| `pilas.php` | Pila (LIFO) | Evaluación de expresiones y análisis sintáctico |
| `colas.php` | Cola (FIFO) | Procesamiento y lectura de tokens |
| `arbol.php` | Árbol | Construcción y representación de árboles sintácticos |

### Conversor de notaciones (raíz del proyecto)

Convierte expresiones entre las tres notaciones: **infija**, **prefija** y **posfija**. Se indica en qué notación está escrita la expresión y se obtienen las tres versiones. Es la página que se abre al entrar a la raíz del proyecto.

- **Operandos:** números (enteros o decimales) y variables.
- **Operadores:** `+`, `-`, `*`, `/`, `%` y `^`, además de paréntesis en la infija.
- **Precedencia:** `^` (3), `*` `/` `%` (2) y `+` `-` (1). El `^` asocia por la derecha (`A^B^C = A^(B^C)`) y el resto por la izquierda.

Ejemplo: `A + B * C` equivale a `A B C * +` en posfija y a `+ A * B C` en prefija.

**Cómo funciona.** Toda conversión se apoya en una pila:

- **Infija a posfija:** algoritmo *Shunting-Yard* con una pila de operadores.
- **Posfija o prefija a infija, y entre prefija y posfija:** una pila de operandos que se van combinando con cada operador (la prefija se recorre de derecha a izquierda).

**Validación.** Detecta caracteres no válidos, expresiones vacías, paréntesis desbalanceados, paréntesis en pre/posfija y expresiones mal formadas, con operandos de más o de menos.

**Detalles a tener en cuenta:**

- En prefija y posfija los operandos van separados por espacios (`A B +`), porque `AB` se lee como una sola variable.
- La infija de salida se genera con los paréntesis mínimos, así que puede verse distinta a la escrita (`((A+B))` se muestra como `A + B`).

Archivos: `index.php` y `vista.php` (interfaz), `src/notaciones.php` (lógica) y `css/style.css` (estilos).

### Calculadora científica (`calculadora/`)

Calculadora responsive con tema claro y oscuro, hecha con HTML, CSS y JavaScript.

- Operaciones básicas, potencias y raíz cuadrada
- Funciones trigonométricas, logarítmicas y exponenciales
- Factorial y constantes (π y e)
- Conversor entre decimal, binario, octal y hexadecimal
- Atajos de teclado y botón para copiar el resultado

Usa [Math.js](https://mathjs.org/) y [SweetAlert2](https://sweetalert2.github.io/).

```
calculadora/
├── index.html
├── css/index.css
└── js/index.js
```

---

## Estructura del proyecto

```
compiladores-toolkit/
├── index.php            # Notaciones
├── vista.php
├── css/style.css
├── src/notaciones.php
├── afd-afnd/
├── estructuras-datos/
├── calculadora/
├── LICENSE
└── README.md
```

---

## Cómo ejecutarlo

Requisitos: **PHP 7.4 o superior** y un servidor local como **XAMPP**.

1. Clona el repositorio dentro de la carpeta `htdocs` de XAMPP:

   ```bash
   git clone https://github.com/juanjose0624/compiladores-toolkit.git
   ```

2. Inicia Apache desde XAMPP.
3. Abre cada módulo en el navegador:

   | Módulo | Dirección |
   |---|---|
   | Notaciones | `http://localhost/compiladores-toolkit/` |
   | Autómatas | `http://localhost/compiladores-toolkit/afd-afnd/` |
   | Estructuras de datos | `http://localhost/compiladores-toolkit/estructuras-datos/` (por ejemplo `pilas.php`) |
   | Calculadora | `http://localhost/compiladores-toolkit/calculadora/` |

---

## Licencia

Distribuido bajo la licencia **MIT**. Consulta el archivo [LICENSE](LICENSE).

## Autor

**Juan José Sepúlveda Álvarez**, estudiante de Ingeniería Informática.

GitHub: [juanjose0624](https://github.com/juanjose0624)