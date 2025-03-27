<?php

use Lawondyss\Parex\Parex;
use Lawondyss\Parex\ParexException;
use Lawondyss\Parex\Parser\ArgvParser;
use Lawondyss\Parex\Result\DynamicResult;

require_once __DIR__ . '/../vendor/autoload.php';

try {
  /** @var DynamicResult{env: string, scopes: string[], currency: string, onlyAccount: string|null, sandbox: bool} $result */
  $result = (new Parex(new ArgvParser()))
    ->addRequire(name: 'env', short: 'e')
    ->addOptional(name: 'scopes', short: 's', multiple: true)
    ->addOptional(name: 'currency', default: 'CZK')
    ->addOptional(name: 'onlyAccount')
    ->addFlag(name: 'sandbox')
    ->parse();

  dump($result);

} catch (ParexException $exc) {
  echo "\n[ERROR] {$exc->getMessage()}\n";
  exit(1);
}
