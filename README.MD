# Simple dependency injection container
Usage:
```php
use PE\Component\SimpleDI\Container;

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
```