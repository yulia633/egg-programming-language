<?php

namespace Egg\Parsers;

/**
 * Egg поддерживает три атомарных элемента: строки, числа, слова.
 * Синтаксический анализатор строит структуру данных, тип которых
 * зависит от того, какому элементу соответсвует строка. Если входные
 * данные не соответсвуют ни одной из трех форм, значит это недопустимое
 * значение и выдается ошибка.
 * @author yulia633 <ignat633yulia@yandex.ru>
 * @version 1.0
 */

/**
 * Принимает строку и возвращает массив, содержащий структуру данных для выражения,
 * содержащегося в начале строки, и часть строки, оставшуюся после синтаксического
 * анализа этого выражения.
 * @param string $program
 * @return array
 */
function parseExpression(string $program): array
{
    $program = skipSpace($program);
    $matches = [];
    $expr = null;
    if ($matches = getString($program)) {
        $expr = getParse(getValueExp($matches[1]), substr($program, strlen($matches[0])));
    } elseif ($matches = getNumber($program)) {
        $expr = getParse(getValueExp($matches[0]), substr($program, strlen($matches[0])));
    } elseif ($matches = getWord($program)) {
        $expr = getParse(getWordExp($matches[0]), substr($program, strlen($matches[0])));
    } else {
        throw new \Exception("Unexpected syntax: {$program}.");
    }

    return parseApply($expr);
}

/**
 * Удаление элементов в начале каждой строки.
 * @param string $string
 * @return string
 */
function skipSpace(string $string): string
{
    $first = preg_match('[\s]', $string);
    if ($first === false) {
        return " ";
    } elseif ($first === 0) {
        return $string;
    } else {
        return ltrim($string, " ");
    }
}

/**
 * Определение атомарного выражения: строка.
 * @param string $text
 * @return mixed
 */
function getString(string $text)
{
    $string = preg_match('/^"([^"]*)"/', $text, $matches, PREG_UNMATCHED_AS_NULL);
    if (!$string || $string === 0) {
        return null;
    }
    return $matches;
}

/**
 * Определение атомарного выражения: число.
 * @param string $text
 * @return mixed
 */
function getNumber(string $text)
{
    $number = preg_match('/^\d+\b/', $text, $matches, PREG_UNMATCHED_AS_NULL);
    if (!is_numeric($number) || $number === 0) {
        return null;
    }
    return $matches;
}

/**
 * Определение атомарного выражения: слово.
 * @param string $text
 * @return mixed
 */
function getWord(string $text)
{
    $word = preg_match('/^[^\s(),#"]+/', $text, $matches, PREG_UNMATCHED_AS_NULL);
    if (!$word || $word === 0) {
        return null;
    }
    return $matches;
}

/**
 * используется в качестве массива для переноса
 * из ParseExpression в остальную часть
 */
function getParse($expr, $rest = null)
{
    return [
        'expr' => $expr,
        'rest' => $rest
    ];
}

function getValueExp($value = null)
{
    return [
        'value' => $value
    ];
}

function getWordExp($name)
{
    return [
        'name' => $name
    ];
}
