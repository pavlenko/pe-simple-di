<?php

namespace PE\Component\SimpleDI;

class Service
{
    /**
     * @var callable
     */
    private $callable;

    /**
     * @param callable $callable
     */
    public function __construct(callable $callable)
    {
        $this->callable = $callable;
    }

    /**
     * @param Container $container
     *
     * @return mixed
     */
    public function __invoke(Container $container)
    {
        return call_user_func($this->callable, $container);
    }

    /**
     * @return callable
     */
    public function getCallable()
    {
        return $this->callable;
    }
}