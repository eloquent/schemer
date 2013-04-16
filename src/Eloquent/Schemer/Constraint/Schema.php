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

use Eloquent\Schemer\Value\ValueInterface;

class Schema implements ConstraintContainerInterface
{
    /**
     * @param array<ConstraintInterface>|null $constraints
     * @param ValueInterface|null             $defaultValue
     * @param string|null                     $title
     * @param string|null                     $description
     */
    public function __construct(
        array $constraints = null,
        ValueInterface $defaultValue = null,
        $title = null,
        $description = null
    ) {
        if (null === $constraints) {
            $constraints = array();
        }

        $this->constraints = $constraints;
        $this->defaultValue = $defaultValue;
        $this->title = $title;
        $this->description = $description;
    }

    /**
     * @return array<ConstraintInterface>
     */
    public function constraints()
    {
        return $this->constraints;
    }

    /**
     * @return ValueInterface|null
     */
    public function defaultValue()
    {
        return $this->defaultValue;
    }

    /**
     * @return string|null
     */
    public function title()
    {
        return $this->title;
    }

    /**
     * @return string|null
     */
    public function description()
    {
        return $this->description;
    }

    /**
     * @return boolean
     */
    public function isEmpty()
    {
        return count($this->constraints()) < 1;
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
    private $defaultValue;
    private $title;
    private $description;
}
