<?php

namespace Lawondyss\Parex\Parser;

use Lawondyss\Parex\Option;
use Lawondyss\Parex\ParexException;

use function array_diff_key;
use function array_flip;
use function array_keys;
use function array_shift;
use function implode;
use function lcfirst;
use function str_contains;
use function str_replace;
use function ucwords;

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

    $oldNames = [];

    foreach ($output as $name => $value) {
      // For kebab-case key creates key with camelCase format for easier access
      // $result->kebabCaseName instead of $result->{"kebab-case-name"}
      if (str_contains($name, '-')) {
        $oldNames[] = $oldName = $name;
        $name = lcfirst(str_replace(' ', '', ucwords(str_replace('-', ' ', $oldName))));
        $output[$name] = $output[$oldName];
      }
    }

    return array_diff_key($output, array_flip($oldNames));
  }
}
