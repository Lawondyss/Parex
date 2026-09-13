<?php

namespace Lawondyss\Parex\Result;

use AllowDynamicProperties;

use function is_int;

#[AllowDynamicProperties]
class DynamicResult extends Result
{
  /** @var array<array-key, mixed> $POSITIONAL */
  public array $POSITIONAL = [];


  /**
   * @param mixed ...$args
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
