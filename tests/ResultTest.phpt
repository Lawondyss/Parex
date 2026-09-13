<?php

declare(strict_types=1);

namespace Lawondyss\Parex\Tests;

use Lawondyss\Parex\ParexException;
use Lawondyss\Parex\Result\DefinedResult;
use Lawondyss\Parex\Result\DynamicResult;
use Tester\Assert;
use Tester\TestCase;

require __DIR__ . '/bootstrap.php';

class CustomDefinedResult extends DefinedResult
{
  public string $env;


  public function __construct(string $env)
  {
    $this->env = $env;
  }
}


/**
 * @testCase
 */
class ResultTest extends TestCase
{
  public function testDynamicResultProperties(): void
  {
    $result = new DynamicResult(env: 'prod', sandbox: true, POSITIONAL: ['build']);

    Assert::same('prod', $result->env);
    Assert::true($result->sandbox);
    Assert::same(['build'], $result->POSITIONAL);
  }


  public function testUndefinedPropertyGet(): void
  {
    $result = new DynamicResult();

    Assert::exception(
      static function () use ($result): void {
        /** @phpstan-ignore-next-line */
        $tmp = $result->nonexistent;
      },
      ParexException::class,
      'Getting an undefined option: nonexistent',
    );
  }


  public function testDefinedResultSetUndefined(): void
  {
    $result = new CustomDefinedResult('prod');

    Assert::exception(
      static function () use ($result): void {
        /** @phpstan-ignore-next-line */
        $result->nonexistent = 'val';
      },
      ParexException::class,
      'Setting an undefined option: nonexistent',
    );
  }


  public function testIsset(): void
  {
    $result = new DynamicResult(env: 'prod', currency: null);

    Assert::true(isset($result->env));
    Assert::false(isset($result->currency));
    Assert::false(isset($result->nonexistent));
    Assert::true(empty($result->nonexistent));
  }
}

(new ResultTest())->run();
