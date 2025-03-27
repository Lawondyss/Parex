<?php

namespace Lawondyss\Parex\Parser;

use Lawondyss\Parex\Option;

use function array_pop;
use function array_shift;
use function explode;
use function in_array;
use function str_contains;
use function str_starts_with;
use function substr;

use const PHP_EOL;

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
    $input = $_SERVER['argv'];

    // first is always the script name
    array_shift($input);

    return $input;
  }


  /**
   * @inheritDoc
   */
  protected function extractValue(Option $option, array $arguments): mixed
  {
    $count = count($arguments);

    $values = [];

    for ($i = 0; $i < $count; $i++) {
      $arg = $arguments[$i];

      if ($arg[0] !== '-') {
        continue;
      }

      if (in_array($arg, ["--{$option->name}", "-{$option->short}"])) {
        $values[] = $arguments[++$i];

      } elseif (str_starts_with($arg, "--{$option->name}=")) {
        $values[] = $this->splitValue($arg);

      } elseif (isset($option->short) && str_starts_with($arg, "-{$option->short}")) {
        $values[] = str_contains($arg, '=')
          ? $this->splitValue($arg)
          : substr($arg, offset: 2);
      }
    }

    return $option->asArray
      ? $values
      : array_pop($values);
  }


  protected function containsFlag(Option $option, array $arguments): bool
  {
    $short = $option->short ?? PHP_EOL;

    return in_array("--{$option->name}", $arguments) || in_array("-{$short}", $arguments);
  }


  private function splitValue(string $s): ?string
  {
    [$_, $value] = explode('=', $s, limit: 2) + [null, null];

    return $value;
  }
}
