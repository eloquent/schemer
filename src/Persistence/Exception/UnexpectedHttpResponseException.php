<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Schemer\Persistence\Exception;

use Exception;
use GuzzleHttp\Message\ResponseInterface;

/**
 * An unexpected HTTP response was received.
 */
final class UnexpectedHttpResponseException extends Exception
{
    /**
     * Construct a new HTTP error exception.
     *
     * @param ResponseInterface $response The response.
     * @param Exception|null    $cause    The cause, if available.
     */
    public function __construct(
        ResponseInterface $response,
        Exception $cause = null
    ) {
        $this->response = $response;

        parent::__construct(
            sprintf(
                'Unexpected HTTP response: %s (%d).',
                var_export($response->getReasonPhrase(), true),
                $response->getStatusCode()
            ),
            0,
            $cause
        );
    }

    /**
     * Get the response.
     *
     * @return ResponseInterface The response.
     */
    public function response()
    {
        return $this->response;
    }

    private $response;
}
