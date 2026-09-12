<?php

declare(strict_types=1);

namespace Lawondyss\Parex\Tests;

use Lawondyss\Parex\Parex;
use Lawondyss\Parex\Parser\GetOptParser;
use Lawondyss\Parex\ParexException;
use Lawondyss\Parex\Result\DynamicResult;
use Tester\Assert;
use Tester\TestCase;

require __DIR__ . '/bootstrap.php';

/**
 * @testCase
 */
class GetOptParserTest extends TestCase
{
  public function testGetOptParserRequiredMissing(): void
  {
    $_SERVER['argv'] = ['script.php'];

    Assert::exception(
      static function (): void {
        (new Parex(new GetOptParser()))
          ->addRequire('env', 'e')
          ->parse();
      },
      ParexException::class,
      'Missing required option(s): --env/-e',
    );
  }
}

(new GetOptParserTest())->run();
