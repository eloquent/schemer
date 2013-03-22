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

class ValidationResult
{
    /**
     * @param array<ValidationIssue>|null $issues
     */
    public function __construct(array $issues = null)
    {
        if (null === $issues) {
            $issues = array();
        }

        $this->issues = $issues;
    }

    /**
     * @return array<ValidationIssue>
     */
    public function issues()
    {
        return $this->issues;
    }

    /**
     * @return boolean
     */
    public function isValid()
    {
        return count($this->issues) < 1;
    }

    private $issues;
}
