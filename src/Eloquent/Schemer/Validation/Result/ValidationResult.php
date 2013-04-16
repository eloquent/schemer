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

use Eloquent\Equality\Comparator;

class ValidationResult
{
    /**
     * @param array<ValidationIssue>|null $issues
     * @param array<ValidationMatch>|null $matches
     * @param Comparator|null             $comparator
     */
    public function __construct(
        array $issues = null,
        array $matches = null,
        Comparator $comparator = null
    ) {
        if (null === $issues) {
            $issues = array();
        }
        if (null === $matches) {
            $matches = array();
        }
        if ($comparator) {
            $comparator = new Comparator;
        }

        $this->issues = $issues;
        $this->matches = $matches;
        $this->comparator = $comparator;
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

    /**
     * @return array<ValidationMatch>
     */
    public function matches()
    {
        return $this->matches;
    }

    /**
     * @return Comparator
     */
    public function comparator()
    {
        return $this->comparator;
    }

    /**
     * @param ValidationResult $result
     *
     * @return ValidationResult
     */
    public function merge(ValidationResult $result)
    {
        return new static(
            array_merge($this->issues, $result->issues()),
            array_merge($this->matches, $result->matches())
        );
    }

    private $issues;
    private $matches;
    private $comparator;
}
