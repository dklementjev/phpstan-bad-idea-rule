# BadIdea PhpStan rule

## Purpose
This is a bit more advanced version of `@deprecated` - call arguments are evaluated, 
the error is produced when condition is true

## Supported attribute targets
Method and function calls are only supported now

## Usage
Unconditional bad idea:
```php
class SomeClass1
{
    #[BadIdea(why: 'Some optional description')]
    public function someMethod(): void
    {
        //...
    }
}
```

Conditional one:
```php
class SomeClass2
{
    #[BadIdea(why: 'Some description', when: 'arguments[1] > 0 && arguments[\'foo\'] >0')]
    public function someMethod(int $foo, float $bar): void
    {
        //...
    }
}
```

[Expression syntax](https://symfony.com/doc/current/reference/formats/expression_language.html)