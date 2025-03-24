<?php

namespace Lawondyss\Parex\Result;

use AllowDynamicProperties;

#[AllowDynamicProperties]
class DynamicResult extends Result
{
  /**
   * @param array<string, string|string[]|null> $args
   */
  public function __construct(...$args)
  {
    foreach ($args as $name => $value) {
      $this->{$name} = $value;
    }
  }
}
