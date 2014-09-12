<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Schemer\Constraint;

/**
 * Represents a complete schema.
 */
class Schema implements SchemaInterface
{
    /**
     * Create an empty schema.
     *
     * @return SchemaInterface An empty schema.
     */
    public static function createEmpty()
    {
        if (null === self::$emptySchema) {
            self::$emptySchema = new self;
        }

        return self::$emptySchema;
    }

    /**
     * Construct a new schema.
     *
     * @param array<ConstraintInterface>|null $constraints  The constraints.
     * @param mixed                           $defaultValue The default value.
     * @param string|null                     $title        The title, or null if there is no title.
     * @param string|null                     $description  The description, or null if there is no description.
     */
    public function __construct(
        array $constraints = null,
        $defaultValue = null,
        $title = null,
        $description = null
    ) {
        if (null === $constraints) {
            $constraints = [];
        }

        $this->constraints = $constraints;
        $this->defaultValue = $defaultValue;
        $this->title = $title;
        $this->description = $description;
    }

    /**
     * Set the constraints.
     *
     * @param array<ConstraintInterface> $constraints The constraints.
     */
    public function setConstraints(array $constraints)
    {
        $this->constraints = $constraints;
    }

    /**
     * Add a constraint.
     *
     * @param ConstraintInterface $constraint The constraint.
     */
    public function addConstraint(ConstraintInterface $constraint)
    {
        $this->constraints[] = $constraint;
    }

    /**
     * Remove a constraint.
     *
     * @param ConstraintInterface $constraint The constraint.
     *
     * @return boolean True if the constraint existed in this schema.
     */
    public function removeConstraint(ConstraintInterface $constraint)
    {
        $isRemoved = false;

        foreach ($this->constraints as $index => $thisConstraint) {
            if ($thisConstraint === $constraint) {
                array_splice($this->constraints, $index, 1);
                $isRemoved = true;

                break;
            }
        }

        return $isRemoved;
    }

    /**
     * Get the constraints.
     *
     * @return array<ConstraintInterface> The constraints.
     */
    public function constraints()
    {
        return $this->constraints;
    }

    /**
     * Get the default value.
     *
     * @return mixed The default value.
     */
    public function defaultValue()
    {
        return $this->defaultValue;
    }

    /**
     * Get the title.
     *
     * @return string|null The title, or null if there is no title.
     */
    public function title()
    {
        return $this->title;
    }

    /**
     * Get the description.
     *
     * @return string|null The description, or null if there is no description.
     */
    public function description()
    {
        return $this->description;
    }

    /**
     * Returns true if there are no constraints.
     *
     * @return boolean True if there are no constraints.
     */
    public function isEmpty()
    {
        return count($this->constraints()) < 1;
    }

    /**
     * Accept the supplied visitor.
     *
     * @param ConstraintVisitorInterface $visitor The visitor to accept.
     *
     * @return mixed The result of visitation.
     */
    public function accept(ConstraintVisitorInterface $visitor)
    {
        return $visitor->visitSchema($this);
    }

    private static $emptySchema;
    private $constraints;
    private $defaultValue;
    private $title;
    private $description;
}
