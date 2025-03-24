<?php

namespace Lawondyss\Parex\Result;

use Lawondyss\Parex\ParexException;

abstract class DefinedResult extends Result
{
  public function __set(string $name, mixed $value): never
  {
    throw new ParexException("Setting an undefined option: {$name}");
  }
}
