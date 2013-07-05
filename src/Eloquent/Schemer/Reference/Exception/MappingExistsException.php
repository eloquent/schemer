<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2013 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Schemer\Reference\Exception;

use Eloquent\Schemer\Pointer\PointerInterface;
use Exception;

final class MappingExistsException extends Exception
{
    /**
     * @param PointerInterface $pointer
     * @param Exception|null   $previous
     */
    public function __construct(
        PointerInterface $pointer,
        Exception $previous = null
    ) {
        $this->pointer = $pointer;

        parent::__construct(
            sprintf(
                'Mapping already exists at pointer %s.',
                var_export($pointer->string(), true)
            ),
            0,
            $previous
        );
    }

    /**
     * @return PointerInterface
     */
    public function pointer()
    {
        return $this->pointer;
    }

    private $pointer;
    private $context;
}
