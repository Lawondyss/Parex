<?php

namespace Lawondyss\Parex\Parser;

use Lawondyss\Parex\Option;

use function array_pop;
use function array_search;
use function array_shift;
use function explode;
use function in_array;
use function str_contains;
use function str_starts_with;
use function substr;

/**
 * Parser utilizing arguments from the array $_SERVER['argv'].
 * A known limitation is that it fails for merged flags (-x -y -z => -xyz); however, this is a parsing issue.
 * TODO: In the fetchArguments() method, the argument array would need preprocessing for flag decomposition.
 */
class ArgvParser extends ParexParser
{
  /**
   * @inheritDoc
   */
  protected function fetchArguments(array $requires, array $optionals, array $flags): array
  {
    $input = $_SERVER['argv'] ?? [];

    if (is_array($input)) {
      // first is always the script name
      array_shift($input);

      return $input;
    }

    return [];
  }


  /**
   * @inheritDoc
   */
  protected function extractValue(Option $option, array &$arguments): mixed
  {
    $count = count($arguments);

    $usedIndexes = [];
    $values = [];

    for ($i = 0; $i < $count; $i++) {
      $arg = $arguments[$i];

      if (!is_string($arg) || $arg === '' || $arg[0] !== '-') {
        continue;
      }

      $targets = ["--{$option->name}"];

      if ($option->short !== null) {
        $targets[] = "-{$option->short}";
      }

      if (in_array($arg, $targets, true)) {
        $usedIndexes[] = $i;

        if (isset($arguments[$i + 1]) && is_string($arguments[$i + 1]) && !str_starts_with($arguments[$i + 1], '-')) {
          $values[] = $arguments[++$i];
          $usedIndexes[] = $i;
        }

      } elseif (str_starts_with($arg, "--{$option->name}=")) {
        $usedIndexes[] = $i;
        $values[] = $this->splitValue($arg);

      } elseif (isset($option->short) && str_starts_with($arg, "-{$option->short}")) {
        $usedIndexes[] = $i;
        $values[] = str_contains($arg, '=')
          ? $this->splitValue($arg)
          : substr($arg, offset: 2);
      }
    }

    // remove used arguments
    foreach ($usedIndexes as $i) {
      unset($arguments[$i]);
    }

    $arguments = array_values($arguments);


    return $option->asArray
      ? $values
      : array_pop($values);
  }


  protected function containsFlag(Option $option, array &$arguments): bool
  {
    $byName = array_search("--{$option->name}", $arguments, strict: true);
    $byShort = $option->short !== null ? array_search("-{$option->short}", $arguments, strict: true) : false;

    // remove used arguments
    if (is_int($byName)) {
      unset($arguments[$byName]);
    }

    if (is_int($byShort)) {
      unset($arguments[$byShort]);
    }

    $arguments = array_values($arguments);

    return is_int($byName) || is_int($byShort);
  }


  private function splitValue(string $s): ?string
  {
    [$_, $value] = explode('=', $s, limit: 2) + [null, null];

    return $value;
  }
}
