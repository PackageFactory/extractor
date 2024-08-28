> [!NOTE]
> This repository is archived, but the project is not. Development continues over here:
> https://codeberg.org/PackageFactory/extractor

# PackageFactory.Extractor

> A fluent interface that allows to validate primitive PHP data structures while also reading them

## Installation

```
composer require --dev packagefactory/extractor
```

## Usage

Let's say, you have a PHP-native array structure like this one:

```php
$configuration = [
    'mailer' => [
        'transport' => 'smtp',
        'host' => 'smtp.example.com',
        'port' => 465
    ]
];
```

It contains configuration for a mailing service. In a lot of PHP projects, configuration comes in this format, usually by being parsed from YAML or JSON sources. While these formats are nicely readable and writable, the result PHP array data structure is completely exempt from type safety.

It is much more desirable to handle the given configuration using a value object like this one:

```php
final class MailerConfiguration
{
    private function __construct(
        public readonly MailerTransport $transport,
        public readonly string $host,
        public readonly int $port
    ) {
    }
}
```

To convert the array structure into this object, it may be suitable to write a static factory method:

```php
final class MailerConfiguration
{
    /* ... */

    public static function fromArray(array $array): self
    {
        if (!isset($array['transport']) || !is_string($array['transport'])) {
            throw new \Exception('Transport must be a string!');
        }

        if (!isset($array['host']) || !is_string($array['host'])) {
            throw new \Exception('Host must be a string!');
        }

        if (!isset($array['port']) || !is_int($array['port'])) {
            throw new \Exception('Port must be an integer!');
        }

        return new self(
            transport: MailerTransport::from($array['transport']),
            host: $array['host'],
            port: $array['port']
        );
    }
}
```

Unfortunately, this is a lot of code to write and it would become even more, if we'd actually like to have more helpful error messages.

This is where the `Extractor` comes in. Using the `Extractor` API, we can write a static factory method like this:

```php
final class MailerConfiguration
{
    /* ... */

    public static function fromExtractor(Extractor $extractor): self
    {
        return new self(
            transport: MailerTransport::from($extractor['transport']->string()),
            host: $extractor['host']->string(),
            port: $extractor['port']->int()
        );
    }
}
```

The extractor handles the runtime type checks for us and throws helpful error messages, if the datastructure doesn't follow our assumptions.

To complete the example from the beginning:

```php
$configuration = [
    'mailer' => [
        'transport' => 'smtp',
        'host' => 'smtp.example.com',
        'port' => 465
    ]
];

$mailerConfiguration = MailerConfiguration::fromExtractor(
    Extractor::for($configuration)['mailer']
);
```

## API

### Type Guards

#### `bool` and `boolOrNull`

```php
Extractor::for(true)->bool(); // returns `true`
Extractor::for(false)->bool(); // returns `false`
Extractor::for(true)->boolOrNull(); // returns `true`
Extractor::for(false)->boolOrNull(); // returns `false`
Extractor::for(null)->boolOrNull(); // returns `null`
```

Checks if the data given to the extractor is a boolean and returns it if thats the case. When `boolOrNull` is used, `null` will pass as well.

#### `int` and `intOrNull`

```php
Extractor::for(42)->int(); // returns `42`
Extractor::for(42)->intOrNull(); // returns `42`
Extractor::for(null)->intOrNull(); // returns `null`
```

Checks if the data given to the extractor is an integer and returns it if thats the case. When `intOrNull` is used, `null` will pass as well.

#### `float` and `floatOrNull`

```php
Extractor::for(47.11)->float(); // returns `47.11`
Extractor::for(47.11)->floatOrNull(); // returns `47.11`
Extractor::for(null)->floatOrNull(); // returns `null`
```

Checks if the data given to the extractor is a float and returns it if thats the case. When `floatOrNull` is used, `null` will pass as well.

#### `intOrFloat` and `intOrFloatOrNull`

```php
Extractor::for(42)->intOrFloat(); // returns `42`
Extractor::for(47.11)->intOrFloat(); // returns `47.11`
Extractor::for(42)->intOrfloatOrNull(); // returns `42`
Extractor::for(47.11)->intOrfloatOrNull(); // returns `47.11`
Extractor::for(null)->intOrfloatOrNull(); // returns `null`
```

In `JSON` there's no distinction between integer and float types. Everything is just a `number`. These two methods check if the data given to the extractor is a float or an integer (and therefore a `number`) and returns it if thats the case. When `intOrfloatOrNull` is used, `null` will pass as well.

#### `string` and `stringOrNull`

```php
Extractor::for('string')->string(); // returns `"string"`
Extractor::for('string')->stringOrNull(); // returns `"string"`
Extractor::for(null)->stringOrNull(); // returns `null`
```

Checks if the data given to the extractor is a string and returns it if thats the case. When `stringOrNull` is used, `null` will pass as well.

#### `array` and `arrayOrNull`

```php
Extractor::for([])->array(); // returns `[]`
Extractor::for([])->arrayOrNull(); // returns `[]`
Extractor::for(null)->arrayOrNull(); // returns `null`
```

Checks if the data given to the extractor is an array and returns it if thats the case. When `stringOrNull` is used, `null` will pass as well.

### Array Access

In order to deal with nested array structures, `Extractor` implements the `\ArrayAccess` interface. 

Given you have an `Extractor` that wraps an array, when you access a key, you'll receive the value for that key wrapped in another `Extractor` instance:

```php
$extractor = Extractor::for([ 'key' => 'value' ]);
$extractor['key']->string(); // returns `"value"`
$extractor['key']->int(); // throws
```

If you access an unknown key, it'll be treated like `Extractor::for(null)`:

```php
$extractor['unknown key']->stringOrNull(); // returns `null`
$extractor['unknown key']->string(); // throws
```

If you access a key on something other than an array, `Extractor` will throw:

```php
$extractor = Extractor::for('This is not an array...');
$extractor['key']; // throws
```

#### `getPath`

Each `Extractor` instance provides you with the access path by which it has been retrieved:

```php
$extractor = Extractor::for([ 
    'some' => [
        'deep' => [
            'path' => '1234'
        ]
    ]
]);

$nested = $extractor['some']['deep']['path'];
var_dump($nested->getPath());
// Output:
// array(3) {
//   [0] =>
//   string(4) "some"
//   [1] =>
//   string(4) "deep"
//   [2] =>
//   string(4) "path"
// }
```

### Iterable

`Extractor` implements the `\IterableAggregate` interface, which allows you to loop over it using `foreach`:

```php
foreach (Extractor::for([ 'key' => 'value' ]) as $key => $value) {
    $key->string(); // returns `"key"`
    $value->string(); // returns `"value"`

    $key->int(); // throws
}
```

As you see, both `$key` and `$value` are themselves instances of `Extractor`.

If you try to iterate over an `Extractor` that wraps something other than an array, the `Extractor` will throw:

```php
foreach (Extractor::for('This is not an array...') as $key => $value) { // throws
}
```

### Error Handling

`Extractor` may throw instances of `ExtractorException`. Each `ExtractorException` carries the access path by which the throwing `Extractor` has been retrieved and tries to provide a helpful error message:

```php
$extractor = Extractor::for([ 
    'some' => [
        'deep' => [
            'path' => '1234'
        ]
    ]
]);

try {
    $extractor['some']['deep']['path']->int();
} catch (ExtractorException $e) {
    var_dump($e->getPath());
    // Output:
    // array(3) {
    //   [0] =>
    //   string(4) "some"
    //   [1] =>
    //   string(4) "deep"
    //   [2] =>
    //   string(4) "path"
    // }

    var_dump($e->getMessage()); 
    // Output:
    // string(65) "Value was expected to be of type int, got string("1234") instead."
}
```

## Contribution

We will gladly accept contributions. Please send us pull requests.

## License

see [LICENSE](./LICENSE)
