<?php

namespace Lawondyss\Parex\Parser;

use Lawondyss\Parex\Option;
use Lawondyss\Parex\ParexException;

use function array_keys;
use function array_merge;
use function array_pop;
use function array_unique;
use function getopt;
use function implode;
use function is_array;

/**
 * Parser using native function getopt() to parse CLI arguments.
 * A known limitation is failing for positional arguments at the beginning of a terminal command.
 * Use only for dash and double dash options (-x and --xxx).
 *
 * @link https://www.php.net/manual/en/function.getopt.php
 */
class GetOptParser implements Parser
{
  /**
   * @param Option[] $requires
   * @param Option[] $optionals
   * @param Option[] $flags
   * @return array<string, mixed>
   * @throws ParexException
   */
  public function parse(array $requires, array $optionals, array $flags): array
  {
    $missing = [];
    $output = [];
    $result = getopt(...$this->createGetOptArguments($requires, $optionals, $flags));

    foreach ($requires as $opt) {
      $value = $this->extractValue($opt, $result);

      if ($value === null) {
        $missing[] = $opt->name;
        continue;
      }

      $output[$opt->name] = $value;
    }

    $missing !== [] && throw new ParexException('Missing required option(s): ' . implode(', ', $missing));

    foreach ($optionals as $opt) {
      $value = $this->extractValue($opt, $result);
      $output[$opt->name] = $value ?? ($opt->asArray ? [] : null);
    }

    foreach ($flags as $opt) {
      $output[$opt->name] = isset($result[$opt->name]) || isset($result[$opt->short]);
    }

    return $output;
  }


  /**
   * @param Option[] $requires
   * @param Option[] $optionals
   * @param Option[] $flags
   * @return array<string|string[]> [string, string[]]
   */
  private function createGetOptArguments(array $requires, array $optionals, array $flags): array
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

    return [$shortOptions, $longOptions];
  }


  /**
   * @param array<string, string|string[]|false> $result
   */
  private function extractValue(Option $opt, array $result): mixed
  {
    // if both variants occur, they must be merged
    $value = isset($result[$opt->name], $result[$opt->short])
      ? array_merge((array)$result[$opt->name], (array)$result[$opt->short])
      : ($result[$opt->name] ?? $result[$opt->short] ?? $opt->default);

    is_array($value) && $value = array_unique($value);

    // type of value by Option
    return match (true) {
      $opt->asArray => (array)$value, // must be always an array, NULL as []
      is_array($value) => array_pop($value), // last_by_short ?? last_by_name ?? last_by_default
      default => $value,
    };
  }
}
