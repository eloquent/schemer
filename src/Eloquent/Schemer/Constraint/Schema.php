<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2013 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Schemer\Constraint;

use Eloquent\Schemer\Value\ObjectValue;

class Schema implements ConstraintContainerInterface
{
    /**
     * @param array<ConstraintInterface>|null $constraints
     * @param ObjectValue|null                $additionalProperties
     */
    public function __construct(
        array $constraints = null,
        ObjectValue $additionalProperties = null
    ) {
        if (null === $constraints) {
            $constraints = array();
        }
        if (null === $additionalProperties) {
            $additionalProperties = new ObjectValue;
        }

        $this->constraints = $constraints;
        $this->additionalProperties = $additionalProperties;
    }

    /**
     * @return array<ConstraintInterface>
     */
    public function constraints()
    {
        return $this->constraints;
    }

    /**
     * @return boolean
     */
    public function isEmpty()
    {
        return count($this->constraints()) < 1;
    }

    /**
     * @return ObjectValue
     */
    public function additionalProperties()
    {
        return $this->additionalProperties;
    }

    /**
     * @param ConstraintVisitorInterface $visitor
     *
     * @return mixed
     */
    public function accept(ConstraintVisitorInterface $visitor)
    {
        return $visitor->visitSchema($this);
    }

    private $constraints;
    private $additionalProperties;
}
