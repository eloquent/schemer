<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2013 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Schemer\Serialization\Yaml\Exception;

use Eloquent\Schemer\Serialization\Exception\ThawExceptionInterface;
use Exception;

final class YamlThawException extends Exception implements ThawExceptionInterface
{
    /**
     * @param Exception|null $previous
     */
    public function __construct(Exception $previous = null)
    {
        if (null === $previous) {
            $message = 'Unknown error.';
        } else {
            $message = $previous->getMessage();
        }

        parent::__construct(
            sprintf('Unable to thaw YAML data. %s', $message),
            0,
            $previous
        );
    }
}
