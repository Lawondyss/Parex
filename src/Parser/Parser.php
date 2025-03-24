<?php

namespace Lawondyss\Parex\Parser;

use Lawondyss\Parex\Option;
use Lawondyss\Parex\ParexException;

interface Parser
{
  /**
   * @param Option[] $requires
   * @param Option[] $optionals
   * @param Option[] $flags
   * @return array<string, string|string[]|null>
   * @throws ParexException
   */
  public function parse(array $requires, array $optionals, array $flags): array;
}
