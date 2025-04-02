# Parex: Command-Line Parser

Parex is a lightweight and flexible PHP library for parsing command-line arguments. It simplifies the process of
defining required, optional, and flag options, and provides a clean and intuitive way to access the parsed values.

## Features

* **Easy Definition:** Define required, optional, and flag options with a simple fluent interface.
* **Default Values:** Set default values for optional arguments.
* **Multiple Values:** Handle options that can accept multiple values.
* **Flexible Result Handling:** Supports both dynamic and statically defined result classes.
* **No Undefined Properties:** Prevents access to undefined properties on result objects.

## Installation

You can install Parex via Composer.

```shell
composer require lawondyss/parex
```

## Usage

Parex allows you to define required, optional, and flag options. You can then parse the command-line arguments and
access the parsed values through a result object.

### Basic example (DynamicResult)

Basic definition:

```php
$dynamicResult = (new Parex())
    ->addRequire(name: 'env', short: 'e')
    ->addOptional(name: 'scopes', short: 's', multiple: true)
    ->addOptional(name: 'currency', default: 'CZK')
    ->addOptional(name: 'onlyAccount')
    ->addFlag(name: 'sandbox')
    ->parse();
```
Command: `php examples/simple.php -e "./.env" --sandbox --scopes=last:month --env="../.env" -s="last:week" -stoday`

Dump:
```
Lawondyss\Parex\Result\DynamicResult
    env: './.env'
    scopes: array (3)
       0 => 'last:month'
       1 => 'last:week'
       2 => 'today'
    currency: 'CZK'
    onlyAccount: null
    sandbox: true
    POSITIONAL: array (0)
```

Try [simple.php](./examples/simple.php) example with dynamic result for command arguments.

### Advanced example (TypedResult)

Definition with own class of result.
```php
$scriptResult = (new Parex())
    ->addRequire('env', 'e')
    ->addOptional('scopes', 's', multiple: true)
    ->addOptional('currency', default: 'CZK')
    ->addOptional('onlyAccount')
    ->addFlag('sandbox')
    ->parse(ScriptResult::class);
```
Command: `php examples/typed.php -e "./.env" --sandbox --scopes=last:month --env="../.env" -s="last:week" -stoday`

Dump:
```
ScriptResult
    env: SplFileInfo
       path: './.env'
    scopes: array (3)
       0 => Scope::LastMonth
          value: 'last:month'
       1 => Scope::LastWeek
          value: 'last:week'
       2 => Scope::Today
          value: 'today'
    currency: Currency::CZK
       value: 'CZK'
    onlyAccount: null
    sandbox: true
```

Try the example [typed.php](./examples/typed.php), which shows how to define own result class (`ScriptResult`) with types and enums.

### ArgvParser

Default GetOptParser does not support positional arguments suitable for custom commands and ignores unknown options.
ArgvParser can be used to support these things.
```php
$dynamicResult = (new Parex(new ArgvParser()))
    ->addRequire(name: 'env', short: 'e')
    ->addOptional(name: 'scopes', short: 's', multiple: true)
    ->addOptional(name: 'currency', default: 'CZK')
    ->addOptional(name: 'onlyAccount')
    ->addFlag(name: 'sandbox')
    ->parse();
```
Command: `php examples/argv.php command sub -e "./.env" --sandbox --scopes=last:month --env="../.env" -s="last:week" -stoday`

Dump: 
```
Lawondyss\Parex\Result\DynamicResult
   env: '../.env'
   scopes: array (3)
      0 => 'last:month'
      1 => 'last:week'
      2 => 'today'
   currency: null
   onlyAccount: null
   sandbox: true
   POSITIONAL: array (2)
      0 => 'command'
      1 => 'sub'
```

## Result classes

Parex provides two base result classes:

* **`DynamicResult`:** A dynamic result class that allows you to access parsed values as properties. It's suitable for
  simple use cases where you don't need to define a specific result structure.
* **`DefinedResult`:** An abstract class that you can extend to create your own statically defined result classes. This
  allows you to define specific properties with types and add custom logic.

## Contributing

Contributions are welcome! Please feel free to submit a pull request or open an issue.

## License

This library is open-sourced software licensed under the MIT license.
