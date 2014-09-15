<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Schemer\Reference\Exception;

use Exception;

/**
 * Unable to resolve a reference.
 */
final class ReferenceResolutionException extends Exception
{
    /**
     * Construct a new reference resolution exception.
     *
     * @param string         $referenceUri The reference URI.
     * @param string         $contextUri   The context URI.
     * @param Exception|null $cause        The cause, if available.
     */
    public function __construct($referenceUri, $contextUri, Exception $cause = null)
    {
        $this->referenceUri = $referenceUri;
        $this->contextUri = $contextUri;

        parent::__construct(
            sprintf(
                'Unable to resolve reference %s from context %s.',
                var_export($referenceUri, true),
                var_export($contextUri, true)
            ),
            0,
            $cause
        );
    }

    /**
     * Get the reference URI.
     *
     * @return string The reference URI.
     */
    public function referenceUri()
    {
        return $this->referenceUri;
    }

    /**
     * Get the context URI.
     *
     * @return string The context URI.
     */
    public function contextUri()
    {
        return $this->contextUri;
    }

    private $referenceUri;
    private $contextUri;
}
