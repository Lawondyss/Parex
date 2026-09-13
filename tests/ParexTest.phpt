<?php

declare(strict_types=1);

namespace Lawondyss\Parex\Tests;

use Lawondyss\Parex\Parex;
use Lawondyss\Parex\Parser\ArgvParser;
use Lawondyss\Parex\ParexException;
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


class TypedResultForTest extends DefinedResult
{
  public string $env;


  public function __construct(string $env)
  {
    $this->env = $env;
  }
}


class TypedResultWithPositionalForTest extends DefinedResult
{
  public string $env;
  /** @var string[] $positional */
  public array $positional;


  public function __construct(string $env, array $positional = [])
  {
    $this->env = $env;
    $this->positional = $positional;
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


  public function testCustomResultWithPositionalArgs(): void
  {
    $_SERVER['argv'] = ['script.php', 'build', '-e', 'prod'];

    /** @var TypedResultForTest $result */
    $result = (new Parex(new ArgvParser()))
      ->addRequire('env', 'e')
      ->parse(TypedResultForTest::class);

    Assert::type(TypedResultForTest::class, $result);
    Assert::same('prod', $result->env);
  }


  public function testCustomResultWithPositionalParameter(): void
  {
    $_SERVER['argv'] = ['script.php', 'build', '-e', 'prod'];

    /** @var TypedResultWithPositionalForTest $result */
    $result = (new Parex(new ArgvParser()))
      ->addRequire('env', 'e')
      ->parse(TypedResultWithPositionalForTest::class);

    Assert::type(TypedResultWithPositionalForTest::class, $result);
    Assert::same('prod', $result->env);
    Assert::same(['build'], $result->positional);
  }


  public function testKebabCaseCollision(): void
  {
    $_SERVER['argv'] = ['script.php', '--my-option=a', '--myOption=b'];

    Assert::exception(
      static function (): void {
        (new Parex(new ArgvParser()))
          ->addRequire('my-option')
          ->addRequire('myOption')
          ->parse();
      },
      ParexException::class,
      "Option name collision for 'myOption'",
    );
  }
}

(new ParexTest())->run();
