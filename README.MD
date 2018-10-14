# Simple dependency injection container

## Requirements

The following versions of PHP are supported.

* PHP 5.5+

## Installation

To install, use composer:

```
php composer.phar require pe/component-simple-di
```

## Usage

```php
<?php
use PE\Component\SimpleDI\Container;
use PE\Component\SimpleDI\ServiceProviderInterface;

// Instantiate container
$di = new Container();

// Add simple value, can be any value
$di->set('foo', 'bar');
$di->get('foo'); //-> just return 'bar'

// Add service definition (closure factory)
$di->set('foo', $di->service(function () {
    return new \stdClass();
}));

$di->get('foo'); //-> call instantiator function and returns instance
$di->get('foo'); //-> returns instance same as in previous call

// Also you can register services via provider
// A. Create provider class
class SomeServiceProvider implements ServiceProviderInterface
{
    public function register(Container $container)
    {
        $container->set('foo', $container->service(function () {
            return new \stdClass();
        }));
    }    
}

// B. Register provider
$di->register(new SomeServiceProvider());
```