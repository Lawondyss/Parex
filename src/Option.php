<?php

namespace Lawondyss\Parex;

use function strlen;

class Option
{
  public function __construct(
    public string $name,
    public ?string $short,
    public bool $asArray,
    public mixed $default,
  ) {
    if (isset($this->short) && strlen($this->short) !== 1) {
      throw new ParexException("Short option for {$this->name} must be single character, given: {$this->short}");
    }
  }
}
