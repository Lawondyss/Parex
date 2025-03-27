<?php

use Lawondyss\Parex\Parex;
use Lawondyss\Parex\ParexException;
use Lawondyss\Parex\Result\DefinedResult;

require_once __DIR__ . '/../vendor/autoload.php';


enum Scope: string
{
  case Today = 'today';
  case Yesterday = 'yesterday';
  case LastWeek = 'last:week';
  case LastMonth = 'last:month';
}


enum Currency: string
{
  case CZK = 'CZK';
  case USD = 'USD';
  case EUR = 'EUR';
}


enum Account: string
{
  case KB = 'KB';
  case CS = 'CS';
  case RB = 'RB';
  case SB = 'SB';
}


class ScriptResult extends DefinedResult
{
  public SplFileInfo $env;
  /** @var Scope[] $scopes */
  public array $scopes;
  public Currency $currency;
  public ?Account $onlyAccount;
  public bool $sandbox;


  public function __construct(string $env, array $scopes, string $currency, ?string $onlyAccount, bool $sandbox)
  {
    $this->env = new SplFileInfo($env);
    $this->scopes = array_map(static fn (string $scp) => Scope::from(strtolower($scp)), $scopes);
    $this->currency = Currency::from(strtoupper($currency));
    $this->onlyAccount = $onlyAccount ? Account::from(strtoupper($onlyAccount)) : null;
    $this->sandbox = $sandbox;
  }
}


try {
  /** @var ScriptResult $result */
  $result = (new Parex())
    ->addRequire('env', 'e')
    ->addOptional('scopes', 's', multiple: true)
    ->addOptional('currency', default: 'CZK')
    ->addOptional('onlyAccount')
    ->addFlag('sandbox')
    ->parse(ScriptResult::class);

  dump($result);

} catch (ParexException $exc) {
  echo "\n[ERROR] {$exc->getMessage()}\n";
  exit(1);
}
