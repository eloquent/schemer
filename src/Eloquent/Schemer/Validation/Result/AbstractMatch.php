<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2013 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Schemer\Validation\Result;

use Eloquent\Schemer\Constraint\Schema;
use Eloquent\Schemer\Pointer\PointerInterface;

abstract class AbstractMatch implements MatchInterface
{
    /**
     * @param Schema           $schema
     * @param PointerInterface $pointer
     */
    public function __construct(
        Schema $schema,
        PointerInterface $pointer
    ) {
        $this->schema = $schema;
        $this->pointer = $pointer;
    }

    /**
     * @return Schema
     */
    public function schema()
    {
        return $this->schema;
    }

    /**
     * @return PointerInterface
     */
    public function pointer()
    {
        return $this->pointer;
    }

    private $constraint;
    private $pointer;
}
