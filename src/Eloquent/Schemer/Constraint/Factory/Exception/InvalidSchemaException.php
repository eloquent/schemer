<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2013 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Schemer\Constraint\Factory\Exception;

use Eloquent\Schemer\Validation\Result\ValidationResult;
use Eloquent\Schemer\Validation\Result\IssueRenderer;
use Eloquent\Schemer\Validation\Result\IssueRendererInterface;
use Exception;

final class InvalidSchemaException extends Exception
{
    /**
     * @param ValidationResult            $result
     * @param Exception|null              $previous
     * @param IssueRendererInterface|null $issueRenderer
     */
    public function __construct(
        ValidationResult $result,
        Exception $previous = null,
        IssueRendererInterface $issueRenderer = null
    ) {
        if (null === $issueRenderer) {
            $issueRenderer = new IssueRenderer;
        }

        $this->result = $result;
        $this->issueRenderer = $issueRenderer;

        parent::__construct(
            sprintf("Invalid schema:\n%s", $this->renderResult($result)),
            0,
            $previous
        );
    }

    /**
     * @return ValidationResult
     */
    public function result()
    {
        return $this->result;
    }

    /**
     * @return IssueRendererInterface
     */
    public function issueRenderer()
    {
        return $this->issueRenderer;
    }

    /**
     * @param ValidationResult $result
     *
     * @return string
     */
    protected function renderResult(ValidationResult $result)
    {
        $renderedIssues = array();
        foreach ($result->issues() as $issue) {
            $renderedIssues[] = sprintf(
                '  - %s',
                $this->issueRenderer()->render($issue)
            );
        }

        return implode("\n", $renderedIssues);
    }

    private $result;
    private $issueRenderer;
}
