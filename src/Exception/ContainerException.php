<?php

namespace PE\Component\SimpleDI\Exception;

use Psr\Container\ContainerExceptionInterface;

class ContainerException extends \Exception implements ContainerExceptionInterface, ExceptionInterface
{}