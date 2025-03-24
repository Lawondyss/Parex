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

See [simple.php](./examples/simple.php) example with dynamic result for command arguments.

### Advanced example (TypedResult)

See the example [typed.php](./examples/typed.php), which shows how to use Parex with a statically defined result class (`ScriptResult`) and enums.

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
