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
    $formattedProgram = skipSpace($program);

    $strings = getString($formattedProgram);
    $numbers = getNumber($formattedProgram);
    $words = getWord($formattedProgram);
    if ($strings) {
        $expr = ["type" => "value", "value" => $words[1]];
    } elseif ($numbers) {
        $expr = ["type" => "value", "value" => $words[0]];
    } elseif ($words) {
        $expr = ["type" => "word", "name" => $words[0]];
    } else {
        throw new \Exception("Неожиданный синтаксис: {$formattedProgram}.");
    }
    return parseApply($expr, substr($formattedProgram, strlen($words[0])));
}

/**
 * Проверяет является ли выражение приложением
 * @param array $expr
 * @param string $program
 * @return array
 */
function parseApply(array $expr, string $program): array
{
    $formattedProgram = skipSpace($program);

    if (substr($formattedProgram, 0, 1) != '(') {
        return getExpression($expr, $formattedProgram);
    }

    $program = skipSpace(substr($formattedProgram, 1));
    $expr = ["type" => "apply", "operator" => $expr, "args" => []];

    while (substr($program, 0, 1) != ')') {
        $arg = parseExpression($program);
        $expr["args"][] = $arg["expr"];
        $program = skipSpace($arg["rest"]);

        if (substr($program, 0, 1) === ',') {
            $program = skipSpace(substr($program, 1));
        } elseif (substr($program, 0, 1) != ')') {
            throw new \Exception("Ожидается ',' или ')'.");
        }
    }

    return parseApply($expr, substr($program, 1));
}

/**
 * Удаление элементов в начале каждой строки.
 * @param string $string
 * @return string
 */
function skipSpace(string $program)
{
    return ltrim($program);
}

/**
 * Определение атомарного выражения: строка.
 * @param string $text
 * @return mixed
 */
function getString(string $text)
{
    return preg_match('/^"([^"]*)"/', $text, $matches, PREG_UNMATCHED_AS_NULL);
}

/**
 * Определение атомарного выражения: число.
 * @param string $text
 * @return mixed
 */
function getNumber(string $text)
{
    return preg_match('/^\d+\b/', $text, $matches, PREG_UNMATCHED_AS_NULL);
}

/**
 * Определение атомарного выражения: слово.
 * @param string $text
 * @return mixed
 */
function getWord(string $text)
{
    $word = preg_match('/^[^\s(),#"]+/', $text, $matches, PREG_UNMATCHED_AS_NULL);
    return $matches;
}

/**
 * используется в качестве массива для переноса
 * из ParseExpression в остальную часть
 */
function getExpression($expr, $rest)
{
    return [
        'expr' => $expr,
        'rest' => $rest
    ];
}

/**
 * Функция-обработчик проверяет достигнут ли конец входной строки,
 * и если да, возвращает выражение.
 * @param string $program
 * @return array
 */
function parse(string $program): array
{
    $resultExpr = parseExpression($program);
    $expression = $resultExpr["expr"];
    $rest = $resultExpr["rest"];

    if (skipSpace(strlen($rest)) > 0) {
        throw new \Exception("Неожиданный текст после программы");
    }
    return $expression;
}
