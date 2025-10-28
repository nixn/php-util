# php-util
A PHP library with utility functions, mostly useful when coding in functional style, but also some other stuff.

![GitHub License](https://img.shields.io/github/license/nixn/php-util)
[![Quality Assurance](https://github.com/nixn/php-util/actions/workflows/quality-assurance.yml/badge.svg)](https://github.com/nixn/php-util/actions/workflows/quality-assurance.yml)
[![Packagist Version](https://img.shields.io/packagist/v/nixn/php-util)][packagist]
[![Packagist Downloads](https://img.shields.io/packagist/dt/nixn/php-util?color=blue)][packagist]
![Packagist Dependency Version](https://img.shields.io/packagist/dependency-v/nixn/php-util/php)
[![API docs](https://img.shields.io/badge/API-docs-blue)](https://nixn.github.io/php-util/namespaces/nixn-php.html)

[packagist]: https://packagist.org/packages/nixn/php-util

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
```php
$array = ['a' => 'A', 'b' => 'B', 1 => 'one', 2 => 'two'];
Arr::pick($array, 'a', 2) // => ['a' => 'A', 2 => 'two']
```

* `find_by(array $array, callable $predicate, bool $return_key = false): mixed`<br>
  Searches for a value in the array, based on the predicate, returning it (or its key) or null, when not found.
```php
$array = ['a' => 'A', 'b' => 'B', 1 => 'one', 2 => 'two'];
$predicate = fn($v, $k) => is_int($k) && $k % 2 == 0;
Arr::find_by($array, $predicate) // => 'two'
Arr::find_by($array, $predicate, true) // => 2
```

* `reduce(iterable $iterable, callable $callback, mixed $initial = <none>, bool $on_empty = false): mixed`<br>
  Like array_reduce(), but the callback passes the key of the element, too. Also, more control over the return value.
```php
$array = ['a' => 'A', 'b' => 'B', 1 => 'one', 2 => 'two'];
$callback = fn($carry, $v, $k) => "$carry|$k";
Arr::reduce($array, $callback, "") // => "|a|b|1|2"
```

* `kvjoin(iterable $iterable, string $sep = ', ', string $kv_sep = '='): string`<br>
  Like implode()/join() in legacy syntax, but outputs the keys too and takes an additional parameter `$kv_sep`.
```php
$array = ['a' => 'A', 'b' => 'B', 1 => 'one', 2 => 'two'];
Arr::kvjoin($array) // => "a=A, b=B, 1=one, 2=two"
```

* `first(iterable $iterable): array`<br>
  Returns the first mapping (element) of an array as key and value in an array, or null/null if the array is empty.
```php
$array = ['a' => 'A', 'b' => 'B', 1 => 'one', 2 => 'two'];
Arr::first($array) // => ['k' => 'a', 'v' => 'A']
Arr::first([]) // => ['k' => null, 'v' => null]
```

* `find(array $array, string|int ...$ks): array`<br>
  Searches for a mapping in the array whose key is one of $keys, returns the key and the value or null/null (if not found) as an array.
```php
$array = ['a' => 'A', 'b' => 'B', 1 => 'one', 2 => 'two'];
Arr::find($array, 'b', 1) // => ['k' => 'b', 'v' => 'B']
Arr::find($array, 'c', 1) // => ['k' => 1, 'v' => 'one']
Arr::find($array, 'c', 3) // => ['k' => null, 'v' => null]
```

The return value for the last two functions is well suited for array destruction and safety on searching:
```php
['k' => $key, 'v' => $value] = Arr::find($array, 2, 'b');
if ($key === null)
    echo "Not Found";
else
    echo "Found value $value with key $key";
// prints: Found value two with key 2
```

### Partial

* `partial(callable $callable, mixed ...$args): callable`<br>
  Returns a new function, which will call the callable with the provided args and can use placeholders for runtime args.
```php
$comment_match = Partial::partial(preg_match(...), '/^\\s*(?:#|$)/');
$comment_match('# comment line') // => 1
$comment_match('normal line') // => 0

$keyword_match = Partial::partial(preg_match(...), '/php/i', Partial::PLACEHOLDER, null, 0, Partial::PLACEHOLDER);
$keyword_match('Only PHP makes this possible!', 0) // => 1
$keyword_match('Only PHP makes this possible!', 10) // => 0
```

### Str (ing)

* `trim_prefix(string $string, string $prefix): string`<br>
  Trims a string prefix when it matches.
```php
$str = "abcdef";
Str::trim_prefix($str, 'abc') // => "def"
Str::trim_prefix($str, 'def') // => "abcdef"
```

* `trim_suffix(string $string, string $suffix): string`<br>
  Trims a string suffix when it matches.
```php
$str = "abcdef";
Str::trim_suffix($str, 'abc') // => "abcdef"
Str::trim_suffix($str, 'def') // => "abc"
```

### Util

* `identity(mixed $v): mixed`<br>
  Just returns the input value. Useful in functional programming style.
```php
Util::identity(42) // => 42
```

* `map(mixed $v, ?callable $fn = null, bool $null_on_not = false, bool $func = false, bool $null = true, bool $false = false, bool $empty = false, bool $zero = false): mixed`<br>
  Checks `$v` on (selectable) common falsy values and returns `$fn($v)` or `$v` (when `$fn === null`) when not falsy. Returns `$v` or null otherwise (depending on `$null_on_not`).
```php
$parenthesize = fn($x) => "($x)";
Util::map(0, $parenthesize) // => "(0)"
Util::map(0, $parenthesize, zero: true) // => 0
Util::map(0, $parenthesize, null_on_not: true, zero: true) // => null
Util::map(42, null, func: true) // => 42
Util::map(42, null, null_on_not: true, func: true) // => null
```

* `map_nots(mixed $v, ?callable $fn = null, bool $null_on_not = false, bool $func = false, mixed ... $nots): mixed`<br>
  Like `map()`, but the falsy values are given as arguments (`$nots`).
```php
$parenthesize = fn($x) => "($x)";
Util::map_nots(42, $parenthesize, false, false, 42) // => 42
Util::map_nots(42, $parenthesize, true, false, 42) // => null
Util::map_nots(null, $parenthesize, true, false, 42) // => "()"
```

* `when(mixed $test, mixed $v, ?callable $fn = null, bool $null = true, bool $false = true, bool $empty = false, bool $zero = false): mixed`<br>
  Returns a value based on `$test`, `$v` and `$fn`, testing based on selectable common falsy values. (See PHPdoc for more information.)
```php
$parenthesize = fn($x) => "($x)";
Util::when(false, rand(...), $parenthesize) // => null (rand() not executed)
Util::when(true, rand(...), $parenthesize) // => "(42)"
Util::when(9, sqrt(...)) // => 3 (same as Util::map(9, sqrt(...)) or even just sqrt(9))
Util::when($get_from_database, fn() => $db->get(), fn($result) => $result->deep_value()) // the natural use case of this
Util::when($use_title, fn() => ['title', $this->title], Hiccup::html(...)) // the natural use case for this
Util::when(get_title(), fn($title) => ['title', $title], Hiccup::html(...), empty: true)
```

* `when_nots(mixed $test, mixed $v, ?callable $fn = null, mixed ... $nots): mixed`<br>
  Like `when()`, but the falsy values are given as arguments (`$nots`).

* `tree_path(mixed $element, callable $get_parent, ?callable $while = null): \Generator`<br>
  For any node in a tree structure, get all parents (possibly up to a specific one) and return them from top to bottom.
```php
$leaf = new Node('c', parent: new Node('b', parent: new Node('a')));
foreach (Util::tree_path($leaf, fn($node) => $node->parent) as $node)
    echo "$node->name,";
// prints: a,b,c,
foreach (Util::tree_path($leaf, fn($node) => $node->parent, fn($node) => $node->name !== 'a') as $node)
    echo "$node->name,";
// prints: b,c,
```

* `new(string $class): \Closure`<br>
  Returns a Closure for a constructor call.
```php
$new_color = Util::new(Color::class);
$color = $new_color(0, 127, 255); // $color instanceof Color
```

### Pipe

A `Pipe` object wraps an intial value and pipes it through any function which modifies it.
The resulting value can be accessed at the end.

Example:
```php
<?php
echo (new Pipe('NOW')) // wrap initial value
(strtolower(...)) // map through strtolower() => 'now'
->new(DateTimeImmutable::class, Pipe::PLACEHOLDER, new DateTimeZone('UTC')) // create class
->format("Y_m_d") // call 'format' method magically => '2025_01_01' (that was 'now' not long ago...)
(str_replace(...), '_', '-') // => '2025-01-01'
(explode(...), '-', Pipe::PLACEHOLDER, 2) // => ['2025', '01-01']
->get(0) // => '2025'
(intval(...)) // => 2025
(fn($x) => $x + 1) // => 2026
->value; // unwrap => 2026
// prints: 2026
```

### With

A `With` object wraps an object and can be used to call any method on it with chaining support (handled by the wrapper).
Note that any value returned by the method calls is discarded!

```php
With::new(mysqli::class, 'localhost', 'db_user', 'db_password')
->set_charset('utf8mb4')
->select_db('log')
->autocommit(false)
->begin_transaction()
->execute_query('INSERT INTO ping')
->execute_query('INSERT INTO pong')
->commit()
->close();
```

Or just use it for initialization calls and unwrap it then:
```php
$mysql = With::new(mysqli::class, 'localhost', 'db_user', 'db_password')
->set_charset('utf8mb4')
->select_db('log')
->autocommit(false)
->object; // unwrap
```

## License
Copyright © 2025 nix <https://keybase.io/nixn>

Distributed under the MIT license, available in the file [LICENSE](LICENSE).

## Donations
If you like php-util, please consider dropping some bitcoins to `1nixn9rd4ns8h5mQX3NmUtxwffNZsbDTP`.
