<?php

namespace Devin\Algolia\Exceptions;

class InvalidPathException extends \Exception
{
    public function __construct($path)
    {
        parent::__construct(\sprintf('Invalid path provided: unable to locate file %s', $path));
    }
}
