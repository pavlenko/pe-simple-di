<?php

namespace PE\Component\SimpleDI\Exception;

use Psr\Container\NotFoundExceptionInterface;

class NotFoundException extends \RuntimeException implements NotFoundExceptionInterface, ExceptionInterface
{
    public function __construct($id)
    {
        parent::__construct(sprintf('Identifier "%s" is not defined.', $id));
    }
}