<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2013 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Schemer\Loader\Exception;

use Exception;
use RuntimeException;
use Zend\Uri\UriInterface;

final class LoadException extends RuntimeException implements LoadExceptionInterface
{
    /**
     * @param UriInterface   $uri
     * @param Exception|null $previous
     */
    public function __construct(UriInterface $uri, Exception $previous = null)
    {
        $this->uri = $uri;

        parent::__construct(
            sprintf("Unable to read from %s.", var_export($uri, true)),
            0,
            $previous
        );
    }

    /**
     * @return UriInterface
     */
    public function uri()
    {
        return $this->uri;
    }

    private $uri;
}
