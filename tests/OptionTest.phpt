<?php

declare(strict_types=1);

namespace Lawondyss\Parex\Tests;

use Lawondyss\Parex\Option;
use Lawondyss\Parex\ParexException;
use Tester\Assert;
use Tester\TestCase;

require __DIR__ . '/bootstrap.php';

/**
 * @testCase
 */
class OptionTest extends TestCase
{
  public function testOptionCreation(): void
  {
    $option = new Option(name: 'env', short: 'e', asArray: false, default: 'dev');

    Assert::same('env', $option->name);
    Assert::same('e', $option->short);
    Assert::false($option->asArray);
    Assert::same('dev', $option->default);
  }


  public function testInvalidShortOptionLength(): void
  {
    Assert::exception(
      static function (): void {
        new Option(name: 'env', short: 'env', asArray: false, default: null);
      },
      ParexException::class,
      'Short option for env must be single character, given: env',
    );
  }
}

(new OptionTest())->run();
