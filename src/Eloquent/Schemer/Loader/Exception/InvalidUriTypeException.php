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

use Eloquent\Schemer\Uri\UriInterface;
use Exception;

final class InvalidUriTypeException extends Exception
{
    /**
     * @param UriInterface   $uri
     * @param string         $expectedClass
     * @param Exception|null $previous
     */
    public function __construct(
        UriInterface $uri,
        $expectedClass,
        Exception $previous = null
    ) {
        $this->uri = $uri;
        $this->expectedClass = $expectedClass;

        parent::__construct(
            sprintf(
                'Invalid URI of type %s passed. Instance of %s expected',
                var_export(get_class($uri), true)
                var_export($expectedClass, true)
            ),
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

    /**
     * @return string
     */
    public function expectedClass()
    {
        return $this->expectedClass;
    }

    private $uri;
    private $expectedClass;
}
