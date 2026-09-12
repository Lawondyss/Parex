<?php

declare(strict_types=1);

namespace Lawondyss\Parex\Tests;

use Lawondyss\Parex\Parex;
use Lawondyss\Parex\Result\DynamicResult;
use Tester\Assert;
use Tester\TestCase;

require __DIR__ . '/bootstrap.php';

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
}

(new ParexTest())->run();
