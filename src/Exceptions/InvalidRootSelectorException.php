<?php

declare(strict_types=1);

namespace Devin\Algolia\Exceptions;

class InvalidRootSelectorException extends \Exception
{
    /**
     * Create a new exception instance.
     *
     * @param string          $selector
     * @param \Throwable|null $previous
     */
    public function __construct(string $selector, \Throwable $previous = null)
    {
        parent::__construct(sprintf('The HMTL provided contains no %s root', $selector), 0, $previous);
    }
}
