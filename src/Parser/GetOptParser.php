<?php

namespace Lawondyss\Parex\Parser;

use Lawondyss\Parex\Option;

use function array_merge;
use function array_pop;
use function array_unique;
use function getopt;
use function is_array;

/**
 * Parser using native function getopt() to parse CLI arguments.
 * A known limitation is failing for positional arguments at the beginning of a terminal command.
 * Use only for dash and double dash options (-x and --xxx).
 *
 * @link https://www.php.net/manual/en/function.getopt.php
 */
class GetOptParser extends ParexParser
{
  /**
   * @param Option[] $requires
   * @param Option[] $optionals
   * @param Option[] $flags
   * @return array<array-key, string|string[]>
   */
  protected function fetchArguments(array $requires, array $optionals, array $flags): array
  {
    $exists = [];
    $longOptions = [];
    $shortOptions = '';

    $append = static function (Option $param, string $suffix) use (&$exists, &$longOptions, &$shortOptions): void {
      if ($exists[$param->name] ?? false) {
        return;
      }
      $exists[$param->name] = true;
      $longOptions[] = "{$param->name}{$suffix}";
      isset($param->short) && $shortOptions .= "{$param->short}{$suffix}";
    };

    foreach ($requires as $param) {
      $append($param, suffix: ':');
    }

    foreach ($optionals as $param) {
      $append($param, suffix: '::');
    }

    foreach ($flags as $param) {
      $append($param, suffix: '');
    }

    return getopt($shortOptions, $longOptions);
  }


  /**
   * @param array<string, string|string[]|false> $arguments
   */
  protected function extractValue(Option $option, array $arguments): mixed
  {
    // if both variants occur, they must be merged
    $value = isset($arguments[$option->name], $arguments[$option->short])
      ? array_merge((array)$arguments[$option->name], (array)$arguments[$option->short])
      : ($arguments[$option->name] ?? $arguments[$option->short] ?? $option->default);

    is_array($value) && $value = array_unique($value);

    // type of value by Option
    return match (true) {
      $option->asArray => (array)$value, // must be always an array, NULL as []
      is_array($value) => array_pop($value), // last_by_short ?? last_by_name ?? last_by_default
      default => $value,
    };
  }


  protected function containsFlag(Option $option, array $arguments): bool
  {
    return isset($arguments[$option->name]) || isset($arguments[$option->short]);
  }
}
