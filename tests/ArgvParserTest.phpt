<?php

declare(strict_types=1);

namespace Lawondyss\Parex\Tests;

use Lawondyss\Parex\Parex;
use Lawondyss\Parex\Parser\ArgvParser;
use Lawondyss\Parex\ParexException;
use Lawondyss\Parex\Result\DynamicResult;
use Tester\Assert;
use Tester\TestCase;

require __DIR__ . '/bootstrap.php';

/**
 * @testCase
 */
class ArgvParserTest extends TestCase
{
  public function testArgvParserSuccess(): void
  {
    $_SERVER['argv'] = [
      'script.php',
      '-e',
      'prod',
      '--scopes=read',
      '--scopes',
      'write',
      '--sandbox',
    ];

    $result = (new Parex(new ArgvParser()))
      ->addRequire('env', 'e')
      ->addOptional('scopes', multiple: true)
      ->addOptional('currency', default: 'CZK')
      ->addFlag('sandbox')
      ->parse();

    Assert::type(DynamicResult::class, $result);
    Assert::same('prod', $result->env);
    Assert::same(['read', 'write'], $result->scopes);
    Assert::same('CZK', $result->currency);
    Assert::true($result->sandbox);
  }


  public function testArgvParserMissingRequired(): void
  {
    $_SERVER['argv'] = ['script.php', '--sandbox'];

    Assert::exception(
      static function (): void {
        (new Parex(new ArgvParser()))
          ->addRequire('env', 'e')
          ->parse();
      },
      ParexException::class,
      'Missing required option(s): --env/-e',
    );
  }


  public function testArgvParserUnknownArgument(): void
  {
    $_SERVER['argv'] = ['script.php', '-e', 'prod', '--unknown'];

    Assert::exception(
      static function (): void {
        (new Parex(new ArgvParser()))
          ->addRequire('env', 'e')
          ->parse();
      },
      ParexException::class,
      'Unknown argument(s): --unknown',
    );
  }


  public function testArgvParserPositionalAndKebab(): void
  {
    $_SERVER['argv'] = [
      'script.php',
      'command',
      'subcommand',
      '-e',
      'prod',
      '--only-account=ACC123',
    ];

    $result = (new Parex(new ArgvParser()))
      ->addRequire('env', 'e')
      ->addOptional('only-account')
      ->parse();

    Assert::same(['command', 'subcommand'], $result->POSITIONAL);
    Assert::same('prod', $result->env);
    Assert::same('ACC123', $result->onlyAccount);
  }


  public function testEmptyStringArgument(): void
  {
    $_SERVER['argv'] = ['script.php', 'cmd', '', '-e', 'prod'];

    $result = (new Parex(new ArgvParser()))
      ->addRequire('env', 'e')
      ->parse();

    Assert::same('prod', $result->env);
    Assert::same(['cmd', ''], $result->POSITIONAL);
  }


  public function testOptionDoesNotConsumeFlag(): void
  {
    $_SERVER['argv'] = ['script.php', '--env', '--sandbox'];

    Assert::exception(
      static function (): void {
        (new Parex(new ArgvParser()))
          ->addRequire('env', 'e')
          ->addFlag('sandbox')
          ->parse();
      },
      ParexException::class,
      'Missing required option(s): --env/-e',
    );
  }


  public function testPositionalArgumentsAfterOptions(): void
  {
    $_SERVER['argv'] = ['script.php', '-e', 'prod', 'build'];

    $result = (new Parex(new ArgvParser()))
      ->addRequire('env', 'e')
      ->parse();

    Assert::same('prod', $result->env);
    Assert::same(['build'], $result->POSITIONAL);
  }


  public function testFlagWithoutShortOption(): void
  {
    $_SERVER['argv'] = ['script.php', '-e', 'prod', '--sandbox'];

    $result = (new Parex(new ArgvParser()))
      ->addRequire('env', 'e')
      ->addFlag('sandbox')
      ->parse();

    Assert::same('prod', $result->env);
    Assert::true($result->sandbox);
  }
}

(new ArgvParserTest())->run();
