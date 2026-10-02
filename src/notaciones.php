<?php
/**
 * Conversor de notaciones: infija, prefija y posfija.
 * Soporta operandos (números y variables), + - * / % ^ y paréntesis.
 * ^ es asociativo por la derecha; el resto por la izquierda.
 *
 * Idea general: toda conversión se apoya en una PILA.
 *  - Infija  -> posfija : pila de operadores (Shunting-Yard).
 *  - Pre/pos -> otras   : pila de operandos que se van combinando.
 */

// Precedencia de cada operador: a mayor número, se evalúa antes.
const PREC = ['+' => 1, '-' => 1, '*' => 2, '/' => 2, '%' => 2, '^' => 3];

// ¿El token es un operador? (si no lo es, se trata como operando)
function esOperador(string $t): bool { return isset(PREC[$t]); }
// Solo ^ asocia por la derecha: A^B^C = A^(B^C).
function esDerecha(string $op): bool { return $op === '^'; }

/* ---------------------------------------------------------------
 * 1. TOKENIZADOR
 * --------------------------------------------------------------- */

/**
 * Divide la expresión en tokens (números, identificadores, operadores, paréntesis).
 * Ojo: en prefija/posfija los operandos deben ir separados por espacio
 * ("A B +"), porque "AB" se lee como una sola variable.
 */
function tokenizar(string $expr): array {
    preg_match_all('/\d+(?:\.\d+)?|[A-Za-z_]\w*|[-+*\/^%()]/', $expr, $m);
    // Si al juntar los tokens (sin espacios) no obtenemos la expresión original,
    // hubo algún carácter que el patrón no reconoció.
    $limpio = preg_replace('/\s+/', '', $expr);
    if (implode('', $m[0]) !== $limpio) {
        throw new InvalidArgumentException("La expresión contiene caracteres no válidos.");
    }
    if (!$m[0]) throw new InvalidArgumentException("La expresión está vacía.");
    return $m[0];
}

/* ---------------------------------------------------------------
 * 2. INFIJA -> POSFIJA
 * --------------------------------------------------------------- */

/** INFIJA -> POSFIJA (algoritmo Shunting-Yard con pila de operadores). */
function infijaAPosfija(array $tokens): array {
    $salida = [];   // resultado en posfija
    $pila = [];     // operadores y paréntesis pendientes
    foreach ($tokens as $t) {
        if ($t === '(') {
            $pila[] = $t;
        } elseif ($t === ')') {
            // Vaciar la pila hasta encontrar el '(' que le corresponde.
            while ($pila && end($pila) !== '(') $salida[] = array_pop($pila);
            if (!$pila) throw new InvalidArgumentException("Paréntesis desbalanceados.");
            array_pop($pila); // descarta el '('
        } elseif (esOperador($t)) {
            // Antes de apilar, sacar los operadores del tope que deben ir primero:
            // los de mayor precedencia, o igual precedencia si el actual asocia por la izquierda.
            while ($pila && esOperador(end($pila))) {
                $tope = end($pila);
                if (PREC[$tope] > PREC[$t] || (PREC[$tope] === PREC[$t] && !esDerecha($t))) {
                    $salida[] = array_pop($pila);
                } else break;
            }
            $pila[] = $t;
        } else {
            $salida[] = $t; // operando: pasa directo a la salida
        }
    }
    // Al terminar, lo que quede en la pila va a la salida (un '(' aquí es un error).
    while ($pila) {
        $op = array_pop($pila);
        if ($op === '(') throw new InvalidArgumentException("Paréntesis desbalanceados.");
        $salida[] = $op;
    }
    validar($salida, false); // detecta operandos/operadores de más o de menos
    return $salida;
}

/* ---------------------------------------------------------------
 * 3. A INFIJA (POSFIJA -> INFIJA y PREFIJA -> INFIJA)
 * --------------------------------------------------------------- */

/**
 * Une dos subexpresiones con un operador. Cada subexpresión es [texto, precedencia].
 * Solo pone paréntesis cuando hacen falta; los operandos simples tienen precedencia 100.
 */
function combinarInfija(string $op, array $a, array $b): array {
    $p = PREC[$op];
    // Izquierda: paréntesis si tiene menor precedencia (o igual y el operador asocia por la derecha).
    $izq = ($a[1] < $p || ($a[1] === $p && esDerecha($op))) ? "({$a[0]})" : $a[0];
    // Derecha: paréntesis si tiene menor precedencia (o igual y el operador asocia por la izquierda).
    $der = ($b[1] < $p || ($b[1] === $p && !esDerecha($op))) ? "({$b[0]})" : $b[0];
    return ["$izq $op $der", $p];
}

/** POSFIJA -> INFIJA */
function posfijaAInfija(array $tokens): string {
    validar($tokens, false);
    $pila = [];
    foreach ($tokens as $t) {
        if (esOperador($t)) {
            // El último en entrar es el operando derecho; el anterior, el izquierdo.
            $b = array_pop($pila);
            $a = array_pop($pila);
            $pila[] = combinarInfija($t, $a, $b);
        } else {
            $pila[] = [$t, 100];
        }
    }
    return $pila[0][0];
}

/** PREFIJA -> INFIJA (se recorre de derecha a izquierda) */
function prefijaAInfija(array $tokens): string {
    validar($tokens, true);
    $pila = [];
    foreach (array_reverse($tokens) as $t) {
        if (esOperador($t)) {
            // Al ir al revés, el primero que sale es el operando izquierdo.
            $a = array_pop($pila);
            $b = array_pop($pila);
            $pila[] = combinarInfija($t, $a, $b);
        } else {
            $pila[] = [$t, 100];
        }
    }
    return $pila[0][0];
}

/* ---------------------------------------------------------------
 * 4. ENTRE PREFIJA Y POSFIJA
 * --------------------------------------------------------------- */

/** POSFIJA -> PREFIJA: el operador pasa al frente de sus dos operandos. */
function posfijaAPrefija(array $tokens): array {
    validar($tokens, false);
    $pila = [];   // cada elemento es una subexpresión ya convertida (lista de tokens)
    foreach ($tokens as $t) {
        if (esOperador($t)) {
            $b = array_pop($pila);
            $a = array_pop($pila);
            $pila[] = array_merge([$t], $a, $b);   // op a b
        } else {
            $pila[] = [$t];
        }
    }
    return $pila[0];
}

/** PREFIJA -> POSFIJA: el operador pasa al final de sus dos operandos. */
function prefijaAPosfija(array $tokens): array {
    validar($tokens, true);
    $pila = [];
    foreach (array_reverse($tokens) as $t) {
        if (esOperador($t)) {
            $a = array_pop($pila);
            $b = array_pop($pila);
            $pila[] = array_merge($a, $b, [$t]);   // a b op
        } else {
            $pila[] = [$t];
        }
    }
    return $pila[0];
}

/** INFIJA -> PREFIJA (vía posfija) */
function infijaAPrefija(array $tokens): array {
    return posfijaAPrefija(infijaAPosfija($tokens));
}

/* ---------------------------------------------------------------
 * 5. VALIDACIÓN Y PUNTO DE ENTRADA
 * --------------------------------------------------------------- */

/**
 * Verifica que una expresión pre/posfija esté bien formada.
 * Se lleva la "altura" de la pila: un operando la sube en 1 y un operador
 * consume 2 y deja 1 (baja 1). Al final debe quedar exactamente 1 resultado.
 */
function validar(array $tokens, bool $prefija): void {
    // La prefija se valida de derecha a izquierda, igual que se evalúa.
    $secuencia = $prefija ? array_reverse($tokens) : $tokens;
    $altura = 0;
    foreach ($secuencia as $t) {
        if ($t === '(' || $t === ')') throw new InvalidArgumentException("Paréntesis no permitidos en pre/posfija.");
        if (esOperador($t)) {
            if ($altura < 2) throw new InvalidArgumentException("Expresión mal formada (faltan operandos).");
            $altura--;
        } else {
            $altura++;
        }
    }
    if ($altura !== 1) throw new InvalidArgumentException("Expresión mal formada (sobran operandos).");
}

/**
 * Convierte desde el modo indicado y devuelve las tres notaciones.
 * Nota: la infija de salida se regenera con paréntesis mínimos, así que puede
 * verse distinta a la escrita (ej. "((A+B))" -> "A + B").
 */
function convertir(string $expr, string $modo): array {
    // Pre/posfija: separar los operandos con espacios ("A B +"), no pegados ("AB+").
    $tokens = tokenizar($expr);
    
    switch ($modo) {
        case 'infija':
            $pos = infijaAPosfija($tokens);
            return ['infija' => posfijaAInfija($pos), 'prefija' => implode(' ', posfijaAPrefija($pos)), 'posfija' => implode(' ', $pos)];
        case 'prefija':
            return ['infija' => prefijaAInfija($tokens), 'prefija' => implode(' ', $tokens), 'posfija' => implode(' ', prefijaAPosfija($tokens))];
        case 'posfija':
            return ['infija' => posfijaAInfija($tokens), 'prefija' => implode(' ', posfijaAPrefija($tokens)), 'posfija' => implode(' ', $tokens)];
    }
    throw new InvalidArgumentException("Modo desconocido.");
}