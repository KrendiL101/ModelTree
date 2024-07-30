# Laravel Nova ModelTree tool
> Simple package for manage tree models (category, menu, catalog, etc.)

Support Nova 4

## Requirements
The model must have columns `parent_id` and `order`, both type int.

## Installation
Start with installing the package
`composer require krendil101/ModelTree`

Then register the tool inside the `NovaServiceProvider.php`

```php
public function tools()
{
    return [new ModelTree];
}
```

Add to menu, in the `resource()` method, insert your resource model

```php
(new Krendil101\ModelTree\ModelTree)
    ->resource(Category::class)
    ->menu($request)
```