<?php

namespace PETest\Component\SimpleDI;

use PE\Component\SimpleDI\Container;
use PE\Component\SimpleDI\Exception\ContainerException;
use PE\Component\SimpleDI\Exception\FrozenException;
use PE\Component\SimpleDI\Exception\NotFoundException;
use PE\Component\SimpleDI\Service;
use PE\Component\SimpleDI\ServiceProviderInterface;

class ContainerTest extends \PHPUnit_Framework_TestCase
{
    public function provideSimpleValues()
    {
        return [
            ['string'],
            [100],
            [100.0],
            [true],
            [false],
            [function(){}]
        ];
    }

    /**
     * @param mixed $value
     *
     * @dataProvider provideSimpleValues
     */
    public function testSimpleValues($value)
    {
        $di = new Container();
        $di->set('foo', $value);

        static::assertSame($value, $di->get('foo'));
    }

    public function testAPI()
    {
        $di = new Container();
        $di->set('foo', 'bar');

        static::assertTrue($di->has('foo'));

        $di->remove('foo');

        static::assertFalse($di->has('foo'));

        static::assertInstanceOf(Service::class, $di->service(function(){}));
    }

    public function testGetServiceExecuteFactory()
    {
        $service = $this->createMock(Service::class);
        $service->expects(static::once())->method('__invoke');

        $di = new Container();
        $di->set('foo', $service);
        $di->get('foo');
    }

    public function testGetThrowsNotFoundException()
    {
        $this->expectException(NotFoundException::class);

        $di = new Container();
        $di->get('foo');
    }

    public function testSetThrowsFrozenException()
    {
        $this->expectException(FrozenException::class);

        $di = new Container();
        $di->set('foo', 'bar');
        $di->get('foo');
        $di->set('foo', 'baz');
    }

    public function testRemoveThrowsFrozenException()
    {
        $this->expectException(FrozenException::class);

        $di = new Container();
        $di->set('foo', 'bar');
        $di->get('foo');
        $di->remove('foo');
    }

    public function testRegisterServiceProviderShouldCallItRegisterMethod()
    {
        /* @var $provider ServiceProviderInterface|\PHPUnit_Framework_MockObject_MockObject */
        $provider = $this->createMock(ServiceProviderInterface::class);
        $provider->expects(static::once())->method('register');

        $di = new Container();
        $di->register($provider);
    }

    public function testExtendThrowNotFoundExceptionIfItemNotExists()
    {
        $this->expectException(NotFoundException::class);

        $di = new Container();
        $di->extend('foo', function(){});
    }

    public function testExtendThrowFrozenExceptionIfItemIsUsed()
    {
        $this->expectException(FrozenException::class);

        $di = new Container();
        $di->set('foo', 'bar');
        $di->get('foo');
        $di->extend('foo', function(){});
    }

    public function testExtendThrowContainerExceptionIfItemIsNotAService()
    {
        $this->expectException(ContainerException::class);

        $di = new Container();
        $di->set('foo', 'bar');
        $di->extend('foo', function(){});
    }

    public function testExtend()
    {
        $service = new \stdClass();

        $di = new Container();
        $di->set('foo', $di->service(function() use ($service) {
            return $service;
        }));


        $di->extend('foo', function($service) {
            $service->bar = 'baz';
            return $service;
        });

        $di->get('foo');

        static::assertSame('baz', $service->bar);
    }
}
