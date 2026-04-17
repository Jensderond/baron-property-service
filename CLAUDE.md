## PHP code guidelines

If you touch any PHP code make sure to run the following commands to make sure our code can be accepted for a MR.

Commands:
- `ddev composer lint` - to use laravel/pint to make sure the file adheres to the lint rules.
- `ddev composer phpstan` - to use PHPStan to scan the codebase and looks for both obvious & tricky bugs

# PHP Conventions
- Follow the symfony conventions.
- We are able to use up to the PHP 8.2 features.

## Array value types
Always specify value types for array parameters, properties, and return types using PHPDoc annotations:

- Use `@param array<key, value>` for parameters
- Use `@var array<key, value>` for properties
- Use `@return array<key, value>` for return types

Examples:
- `@param array<string, mixed> $config` - associative array
- `@param array<int, string> $items` - indexed array of strings
- `@param array<string, User> $users` - array of User objects
- `@return array<string, int>` - returns associative array

Never use bare `array` without specifying the key and value types.
