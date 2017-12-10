<?php

namespace PE\Component\SimpleDI\Exception;

class FrozenException extends ContainerException
{
    public function __construct($id)
    {
        parent::__construct(sprintf('Cannot modify frozen item "%s".', $id));
    }
}