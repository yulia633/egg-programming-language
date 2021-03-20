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
 * Принимает строку и возвращает объект, содержащий структуру данных для выражения,
 * содержащегося в начале строки, и часть строки, оставшуюся после синтаксического
 * анализа этого выражения.
 * @param string $program
 * @return array
 */
function parseExpression(string $program): array
{
    $program = skipSpace($program);
    $match;
    $expr;
    if ($match = getString($program)) {
        $expr = [$type => 'value', $value => $match[1]];
    } elseif ($match = getNumber($program)) {
        $expr = [$type => 'value', $value => $match[0]];
    } elseif ($match = getWord($program)) {
        $expr = [$type => 'word', $name => $match[0]];
    } else {
        throw new \Exception("Unexpected syntax: {$program}.");
    }

    return parseApply($expr, substr($program, strlen($match[0])));
}

/**
 * Удаление элементов в начале каждой строки.
 * @param string $string
 * @return string
 */
function skipSpace(string $string): string
{
    $first = preg_match('[\s]', $string);
    if (!$first || $first === 0) {
        return " ";
    }

    return ltrim($string, " ");
}

/**
 * Определение атомарного выражения: строка.
 * @param string $text
 * @return mixed
 */
function getString(string $text): mixed
{
    $string = preg_match('/^[a-z]+$/iu', $text, $matches, PREG_UNMATCHED_AS_NULL);
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
function getNumber(string $text): mixed
{
    $number = preg_match('/^[0-9]+$/iu', $text, $matches, PREG_UNMATCHED_AS_NULL);
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
function getWord(string $text): mixed
{
    if (getNumber($text)) {
        return null;
    }
    if (is_string($text) && !getString($text)) {
        return $text;
    }
}
