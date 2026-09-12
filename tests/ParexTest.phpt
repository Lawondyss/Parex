<?php

declare(strict_types=1);

namespace Lawondyss\Parex\Tests;

use Lawondyss\Parex\Parex;
use Lawondyss\Parex\Parser\ArgvParser;
use Lawondyss\Parex\Result\DefinedResult;
use Lawondyss\Parex\Result\DynamicResult;
use Tester\Assert;
use Tester\TestCase;

require __DIR__ . '/bootstrap.php';

class SampleResult extends DefinedResult
{
  public string $env;
  public string $currency;
  public bool $sandbox;


  public function __construct(string $env, string $currency, bool $sandbox)
  {
    $this->env = $env;
    $this->currency = $currency;
    $this->sandbox = $sandbox;
  }
}


/**
 * @testCase
 */
class ParexTest extends TestCase
{
  public function testParexFluentInterface(): void
  {
    $parex = new Parex();

    Assert::same($parex, $parex->addRequire('env', 'e'));
    Assert::same($parex, $parex->addOptional('currency', default: 'CZK'));
    Assert::same($parex, $parex->addFlag('sandbox'));
  }


  public function testParseTypedDefinedResult(): void
  {
    $_SERVER['argv'] = ['script.php', '-e', 'prod', '--sandbox'];

    $result = (new Parex(new ArgvParser()))
      ->addRequire('env', 'e')
      ->addOptional('currency', default: 'EUR')
      ->addFlag('sandbox')
      ->parse(SampleResult::class);

    Assert::type(SampleResult::class, $result);
    Assert::same('prod', $result->env);
    Assert::same('EUR', $result->currency);
    Assert::true($result->sandbox);
  }
}

(new ParexTest())->run();
