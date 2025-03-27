<?php

namespace Lawondyss\Parex\Parser;

use Lawondyss\Parex\Option;
use Lawondyss\Parex\ParexException;
use function array_pop;
use function array_shift;
use function explode;
use function implode;
use function in_array;
use function str_contains;
use function str_starts_with;
use function substr;
use const PHP_EOL;

class ArgvParser implements Parser
{
  /**
   * @inheritDoc
   */
  public function parse(array $requires, array $optionals, array $flags): array
  {
    $input = $_SERVER['argv'];
    $missing = [];
    $output = [];

    // first is always the script name
    array_shift($input);

    // there is a possibility that the first positions are positional arguments
    // (commands, mandatory arguments)
    $idx = 0;

    while (!str_starts_with($input[$idx], '-')) {
      $output[] = array_shift($input);
    }

    foreach ($requires as $opt) {
      $value = $this->extractValue($opt, $input);

      if ($value === null) {
        $missing[] = $opt->name;
        continue;
      }

      $output[$opt->name] = $value;
    }

    $missing !== [] && throw new ParexException('Missing required option(s): ' . implode(', ', $missing));

    foreach ($optionals as $opt) {
      $value = $this->extractValue($opt, $input);
      $output[$opt->name] = $value ?? ($opt->asArray ? [] : null);
    }

    foreach ($flags as $opt) {
      $output[$opt->name] = $this->containsFlag($opt, $input);
    }

    return $output;
  }


  /**
   * @param string[] $input
   */
  private function extractValue(Option $option, array $input): mixed
  {
    $count = count($input);

    $values = [];

    for ($i = 0; $i < $count; $i++) {
      $arg = $input[$i];

      if ($arg[0] !== '-') {
        continue;
      }

      if (in_array($arg, ["--{$option->name}", "-{$option->short}"])) {
        $values[] = $input[++$i];

      } elseif (str_starts_with($arg, "--{$option->name}=")) {
        [$_, $value] = explode('=', $arg, limit: 2) + [null, null];
        $values[] = $value;

      } elseif (isset($option->short) && str_starts_with($arg, "-{$option->short}")) {
        if (str_contains($arg, '=')) {
          [$_, $value] = explode('=', $arg, limit: 2) + [null, null];
          $values[] = $value;
        } else {
          $values[] = substr($arg, 2);
        }
      }
    }

    return $option->asArray
      ? $values
      : array_pop($values);
  }


  private function containsFlag(Option $option, array $input): bool
  {
    $short = $option->short ?? PHP_EOL;

    return in_array("--{$option->name}", $input) || in_array("-{$short}", $input);
  }
}
