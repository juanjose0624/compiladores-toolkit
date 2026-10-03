<?php
/**
 * Contrato común que deben cumplir todos los autómatas.
 * Al implementarla en AFD y AFN, cualquier variable tipada
 * como Automata garantiza tener el método ejecutar().
 */
interface Automata
{
    public function ejecutar(string $cadena): array;
}
