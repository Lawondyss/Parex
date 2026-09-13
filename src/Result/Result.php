<?php

namespace Lawondyss\Parex\Result;

use Lawondyss\Parex\ParexException;

abstract class Result
{
  public function __get(string $name): never
  {
    throw new ParexException("Getting an undefined option: {$name}");
  }


  public function __isset(string $name): bool
  {
    return property_exists($this, $name) && isset($this->{$name});
  }
}
