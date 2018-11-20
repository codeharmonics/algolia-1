<?php

declare(strict_types=1);

namespace Devin\Algolia\Exceptions;

class InvalidPathException extends \Exception
{
    /**
     * Create a new exception instance.
     *
     * @param string $path
     */
    public function __construct(string $path)
    {
        parent::__construct(\sprintf('Invalid path provided: unable to locate file %s', $path));
    }
}
