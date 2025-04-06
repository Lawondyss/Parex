<?php

namespace Lawondyss\Parex\Parser;

use Lawondyss\Parex\Option;
use Lawondyss\Parex\ParexException;

use function array_keys;
use function array_shift;
use function implode;

abstract class ParexParser implements Parser
{
  /**
   * @param Option[] $requires
   * @param Option[] $optionals
   * @param Option[] $flags
   * @return array<array-key, mixed>
   */
  abstract protected function fetchArguments(array $requires, array $optionals, array $flags): array;


  /**
   * @param array<array-key, mixed> $arguments
   */
  abstract protected function extractValue(Option $option, array &$arguments): mixed;


  /**
   * @param array<array-key, mixed> $arguments
   */
  abstract protected function containsFlag(Option $option, array &$arguments): bool;


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
    $arguments = $this->fetchArguments($requires, $optionals, $flags);

    // positional arguments
    while (($arguments[0] ?? false) && $arguments[0][0] !== '-') {
      $output[] = array_shift($arguments);
    }

    foreach ($requires as $opt) {
      $value = $this->extractValue($opt, $arguments);

      if ($value === null) {
        $missing[] = "--{$opt->name}" . ($opt->short ? "/-{$opt->short}" : '');
        continue;
      }

      $output[$opt->name] = $value;
    }

    $missing !== [] && throw new ParexException('Missing required option(s): ' . implode(', ', $missing));

    foreach ($optionals as $opt) {
      $value = $this->extractValue($opt, $arguments);
      $output[$opt->name] = $value ?? ($opt->asArray ? (array)$opt->default : $opt->default);
    }

    foreach ($flags as $opt) {
      $output[$opt->name] = $this->containsFlag($opt, $arguments);
    }

    $arguments !== [] && throw new ParexException('Unknown argument(s): ' . implode(', ', $arguments));

    return $output;
  }
}
