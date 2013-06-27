<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2013 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Schemer\Value\Exception;

use Exception;

final class UndefinedKeyException extends Exception
{
    /**
     * @param mixed          $key
     * @param Exception|null $previous
     */
    public function __construct($key, Exception $previous = null)
    {
        $this->key = $key;

        parent::__construct(
            sprintf('Undefined key %s.', var_export($key, true)),
            0,
            $previous
        );
    }

    /**
     * @return mixed
     */
    public function key()
    {
        return $this->key;
    }

    private $key;
}
