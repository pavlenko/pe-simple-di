<?php

namespace PE\Component\SimpleDI;

use PE\Component\SimpleDI\Exception\ContainerException;
use PE\Component\SimpleDI\Exception\FrozenException;
use PE\Component\SimpleDI\Exception\NotFoundException;
use Psr\Container\ContainerInterface;

class Container implements ContainerInterface
{
    /**
     * @var array
     */
    private $values = [];

    /**
     * @var array
     */
    private $frozen = [];

    /**
     * @param string $id
     *
     * @return mixed
     *
     * @throws NotFoundException
     */
    public function get($id)
    {
        if (!array_key_exists($id, $this->values)) {
            throw new NotFoundException($id);
        }

        if ($this->values[$id] instanceof Service) {
            $this->values[$id] = $this->values[$id]($this);
        }

        $this->frozen[$id] = true;

        return $this->values[$id];
    }

    /**
     * @param string $id
     *
     * @return bool
     */
    public function has($id)
    {
        return array_key_exists($id, $this->values);
    }

    /**
     * @param string $id
     * @param mixed  $value
     *
     * @return Container
     *
     * @throws FrozenException
     */
    public function set($id, $value)
    {
        if (isset($this->frozen[$id])) {
            throw new FrozenException($id);
        }

        $this->values[$id] = $value;
        return $this;
    }

    /**
     * @param string $id
     *
     * @return Container
     *
     * @throws FrozenException
     */
    public function remove($id)
    {
        if (isset($this->frozen[$id])) {
            throw new FrozenException($id);
        }

        if (array_key_exists($id, $this->values)) {
            unset($this->values[$id]);
        }

        return $this;
    }

    /**
     * @param callable $value
     *
     * @return Service
     */
    public function service(callable $value)
    {
        return new Service($value);
    }

    /**
     * @param string   $id
     * @param callable $callable
     *
     * @return Container
     *
     * @throws ContainerException
     * @throws FrozenException
     * @throws NotFoundException
     */
    public function extend($id, callable $callable)
    {
        if (!array_key_exists($id, $this->values)) {
            throw new NotFoundException($id);
        }

        if (isset($this->frozen[$id])) {
            throw new FrozenException($id);
        }

        if (!($this->values[$id] instanceof Service)) {
            throw new ContainerException('Not extensible item with identifier ' . $id);
        }

        $original = $this->values[$id]->getCallable();
        $extended = function ($c) use ($callable, $original) {
            return $callable($original($c), $c);
        };

        return $this->set($id, $this->service($extended));
    }

    /**
     * @param ServiceProviderInterface $provider
     *
     * @return Container
     */
    public function register(ServiceProviderInterface $provider)
    {
        $provider->register($this);
        return $this;
    }
}