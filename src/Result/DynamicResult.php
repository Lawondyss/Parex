<?php

namespace Lawondyss\Parex\Result;

use AllowDynamicProperties;

use function is_int;

#[AllowDynamicProperties]
class DynamicResult extends Result
{
  /** @var string[] $POSITIONAL */
  public array $POSITIONAL = [];


  /**
   * @param array<array-key, string|string[]|null> $args
   */
  public function __construct(...$args)
  {
    foreach ($args as $name => $value) {
      is_int($name)
        ? $this->POSITIONAL[] = $value
        : $this->{$name} = $value;
    }
  }
}
