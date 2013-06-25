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
     * @param array<MatchInterface>|null  $matches
     */
    public function __construct(array $issues = null, array $matches = null)
    {
        if (null === $issues) {
            $issues = array();
        }
        if (null === $matches) {
            $matches = array();
        }

        $this->issues = $issues;
        $this->matches = $matches;
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
     * @param MatchInterface $match
     */
    public function addMatch(MatchInterface $match)
    {
        $this->matches[] = $match;
    }

    /**
     * @return array<MatchInterface>
     */
    public function matches()
    {
        return $this->matches;
    }

    /**
     * @return array<ValidationMatch>
     */
    public function validationMatches()
    {
        $matches = array();
        foreach ($this->matches() as $match) {
            if ($match instanceof ValidationMatch) {
                $matches[] = $match;
            }
        }

        return $matches;
    }

    /**
     * @return array<DefaultValueMatch>
     */
    public function defaultValueMatches()
    {
        $matches = array();
        foreach ($this->matches() as $match) {
            if ($match instanceof DefaultValueMatch) {
                $matches[] = $match;
            }
        }

        return $matches;
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
}
