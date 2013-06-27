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

final class RelativeUriException extends Exception
{
    /**
     * @param UriInterface   $uri
     * @param Exception|null $previous
     */
    public function __construct(UriInterface $uri, Exception $previous = null)
    {
        $this->uri = $uri;

        parent::__construct(
            sprintf(
                'Unable to read from relative URI %s.',
                var_export($uri->toString(), true)
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

    private $uri;
}
