<?php

namespace PE\Component\SimpleDI;

interface ServiceProviderInterface
{
    /**
     * @param Container $container
     */
    public function register(Container $container);
}