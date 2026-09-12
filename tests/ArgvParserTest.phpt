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
}

(new ArgvParserTest())->run();
