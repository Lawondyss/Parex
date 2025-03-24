<?php

namespace Lawondyss\Parex;

use Lawondyss\Parex\Parser\GetOptParser;
use Lawondyss\Parex\Parser\Parser;
use Lawondyss\Parex\Result\DynamicResult;
use Lawondyss\Parex\Result\Result;

class Parex
{
  /** @var Option[] $requires */
  protected array $requires = [];

  /** @var Option[] $optionals */
  protected array $optionals = [];

  /** @var Option[] $flags */
  protected array $flags = [];

  public function __construct(
    private readonly Parser $parser = new GetOptParser()
  ) {
  }


  /**
   * Define required option of command.
   * Throwing exception if missing.
   * Result value is always an array if multiple is TRUE.
   */
  public function addRequire(string $name, ?string $short = null, bool $multiple = false): static
  {
    $this->requires[] = new Option($name, $short, $multiple, default: null);

    return $this;
  }


  /**
   * Define optional option of command.
   * Set default value if missing.
   * Result value is always an array if multiple is TRUE, empty array for NULL as default.
   */
  public function addOptional(string $name, ?string $short = null, mixed $default = null, bool $multiple = false): static
  {
    $this->optionals[] = new Option($name, $short, $multiple, $default);

    return $this;
  }


  /**
   * Define flag option of command (does not receive value from CLI).
   * Result value is FALSE if missing and TRUE if present.
   */
  public function addFlag(string $name, ?string $short = null): static
  {
    $this->flags[] = new Option($name, $short, asArray: false, default: null);

    return $this;
  }


  /**
   * @template T of Result
   * @param class-string<T> $resultClass
   * @return T
   * @throws ParexException
   */
  public function parse(string $resultClass = DynamicResult::class): Result
  {
    $output = $this->parser->parse($this->requires, $this->optionals, $this->flags);

    // values are bound by name to properties because $output is an associative array
    return new $resultClass(...$output);
  }
}
