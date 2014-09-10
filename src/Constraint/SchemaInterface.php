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
 * The interface implemented by schemas.
 */
interface SchemaInterface extends ConstraintInterface
{
    /**
     * Get the constraints.
     *
     * @return array<ConstraintInterface> The constraints.
     */
    public function constraints();

    /**
     * Get the default value.
     *
     * @return mixed The default value.
     */
    public function defaultValue();

    /**
     * Get the title.
     *
     * @return string|null The title, or null if there is no title.
     */
    public function title();

    /**
     * Get the description.
     *
     * @return string|null The description, or null if there is no description.
     */
    public function description();

    /**
     * Returns true if there are no constraints.
     *
     * @return boolean True if there are no constraints.
     */
    public function isEmpty();
}
