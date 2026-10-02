# AFD-AFND

Simulador web de autómatas finitos escrito en PHP, con interfaz en HTML y CSS. Permite seleccionar entre un **Autómata Finito Determinístico (AFD)** o un **Autómata Finito No Determinístico (AFN)**, ingresar una cadena binaria y ver si es aceptada, junto con la traza paso a paso de las transiciones.

## Autómatas incluidos

### AFD

Reconoce cadenas binarias que **terminan en `01`**.

- Cada estado tiene exactamente una transición por símbolo.
- Estados: `q0`, `q1`, `q2`
- Estado inicial: `q0`
- Estado de aceptación: `q2`

### AFN

Reconoce cadenas binarias que **contienen la subcadena `01`** en algún punto.

- Cada par (estado, símbolo) puede llevar a varios estados a la vez (o a ninguno).
- Estados: `q0`, `q1`, `q2`
- Estado inicial: `q0`
- Estado de aceptación: `q2`

## Arquitectura

El proyecto usa una interfaz común `Automata` con el método `ejecutar(string $cadena): array`, implementada tanto por `AFD` como por `AFN`. Esto permite tratar ambos autómatas de forma polimórfica desde `index.php`.

```
Automata.php   # Interfaz común
AFD.php        # Implementación del autómata determinístico
AFN.php        # Implementación del autómata no determinístico
index.php      # Interfaz web: selección de autómata y ejecución
style.css      # Estilos de la interfaz
```

Cada ejecución devuelve un arreglo con:

- El tipo de autómata (`AFD` o `AFN`)
- La cadena evaluada
- La traza de transiciones paso a paso
- El o los estados finales alcanzados
- Si la cadena fue **aceptada** o no

## Requisitos

- PHP 7.4 o superior
- Servidor local (ej. XAMPP)

## Uso

1. Clona el repositorio en tu carpeta de servidor local (ej. `htdocs` de XAMPP):

   ```
   git clone https://github.com/juanjose0624/AFD-AFND.git
   ```

2. Inicia Apache desde XAMPP.
3. Abre en el navegador:

   ```
   http://localhost/AFD-AFND/index.php
   ```

4. Selecciona el tipo de autómata (AFD o AFN), ingresa una cadena binaria y ejecuta.

## Contexto

Proyecto realizado como taller de la materia de Compiladores.