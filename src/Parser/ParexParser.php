<?php

namespace Lawondyss\Parex\Parser;

use Lawondyss\Parex\Option;
use Lawondyss\Parex\ParexException;

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
  abstract protected function extractValue(Option $option, array $arguments): mixed;


  /**
   * @param array<array-key, mixed> $arguments
   */
  abstract protected function containsFlag(Option $option, array $arguments): bool;


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
    $result = $this->fetchArguments($requires, $optionals, $flags);

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
      $output[$opt->name] = $this->containsFlag($opt, $result);
    }

    return $output;
  }
}
