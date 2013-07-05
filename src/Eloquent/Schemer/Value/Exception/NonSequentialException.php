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

final class NonSequentialException extends Exception
{
    /**
     * @param array          $indices
     * @param Exception|null $previous
     */
    public function __construct(array $indices, Exception $previous = null)
    {
        $this->indices = $indices;

        parent::__construct(
            sprintf(
                'Indices [%s] are non-sequential.',
                implode(
                    ',',
                    array_map(
                        $indices,
                        function ($index) {
                            return var_export($index, true)
                        }
                    )
                )
            ),
            0,
            $previous
        );
    }

    /**
     * @return array
     */
    public function indices()
    {
        return $this->indices;
    }

    private $indices;
}
