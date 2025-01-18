# php-util
A PHP library with utility functions, mostly useful when coding in functional style, but also some other stuff.

## Installation
Via composer:
```
composer require nixn/php-util
```

## Components

Namespace: `nixn\php`. All functions (except in Pipe) are static, so they can be called easily
(e.g. `Arr::pick($array, 'a', 'b'))`).

### Arr (ay)

* `pick(array $array, string|int ...$keys): array`<br>
  Returns a new array with only the key => value mappings left, whose keys are in $keys.
* `find_by(array $array, callable $predicate, bool $return_key = false): mixed`<br>
  Searches for a value in the array, based on the predicate, returning it (or its key) or null, when not found.
* `reduce(array $array, callable $callback, mixed $initial = null): mixed`<br>
  Like array_reduce(), but the callback passes the key of the element, too.
* `kvjoin(array $data, string $sep = ', ', string $kv_sep = '='): string`<br>
  Like implode()/join() in legacy syntax, but outputs the keys too and takes an additional parameter `$kv_sep`.
* `first(array $array): array`<br>
  Returns the first mapping (element) of an array as key and value in an array, or null/null if the array is empty.
* `find(array $array, string|int ...$ks): array`<br>
  Searches for a mapping in the array whose key is one of $keys, returns the key and the value or null/null (if not found) as an array.

### Partial

* `partial(callable $callable, mixed ...$args): callable`<br>
  Returns a new function, which will call the callable with the provided args and can use placeholders for runtime args.

### Str (ing)

* `trim_prefix(string $string, string $prefix): string`<br>
  Trims a string prefix when it matches.
* `trim_suffix(string $string, string $suffix): string`<br>
  Trims a string suffix when it matches.

### Util

* `identity(mixed $v): mixed`<br>
  Just returns the input value. Useful in functional programming style.
* `map(mixed $v, ?callable $fn = null, bool $null_on_not = false, bool $func = false, bool $null = true, bool $false = false, bool $empty = false, bool $zero = false): mixed`<br>
  Checks `$v` on (selectable) common falsy values and returns `$fn($v)` or `$v` (when `$fn === null`) when not falsy. Returns `$v` or null otherwise (depending on `$null_on_not`).
* `map_nots(mixed $v, ?callable $fn = null, bool $null_on_not = false, bool $func = false, mixed ... $nots): mixed`<br>
  Like `map()`, but the falsy values are given as arguments (`$nots`).
* `when(mixed $test, mixed $v, ?callable $fn = null, bool $null = true, bool $false = true, bool $empty = false, bool $zero = false): mixed`<br>
  Returns a value based on `$test`, `$v` and `$fn`, testing based on selectable common falsy values. (See PHPdoc for more information.)
* `when_nots(mixed $test, mixed $v, ?callable $fn = null, mixed ... $nots): mixed`<br>
  Like `when()`, but the falsy values are given as arguments (`$nots`).
* `tree_path(mixed $element, callable $get_parent, ?callable $while = null): \Generator`<br>
  For any node in a tree structure, get all parents (possibly up to a specific one) and return them from top to bottom.
* `new(string $class): \Closure`<br>
  Returns a Closure for a constructor call.

### Pipe

A Pipe wraps an intial value and pipes it through any function which modifies it. The resulting value can be accessed at the end.

Example:
```php
<?php
echo (new Pipe('NOW')) // wrap initial value
(strtolower(...)) // map through strtolower() => 'now'
->new(DateTimeImmutable::class, Pipe::PLACEHOLDER, DateTimeZone::UTC) // create class
->format("Y_m_d") // => '2025_01_01' (that was 'now' not long ago...)
(str_replace(...), '_', '-') // => '2025-01-01'
(explode(...), '-', Pipe::PLACEHOLDER, 2) // => ['2025', '01-01']
->get(0) // => '2025'
(intval(...)) // => 2025 (still wrapped)
->value; // unwrap => 2025
// prints: 2025
```

## License
Copyright © 2025 nix <https://keybase.io/nixn>

Distributed under the MIT license, available in the file [LICENSE](LICENSE).

## Donations
If you like php-util, please consider dropping some bitcoins to `1nixn9rd4ns8h5mQX3NmUtxwffNZsbDTP`.
