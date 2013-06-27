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

use Eloquent\Schemer\Validation\Result\IssueRenderer;
use Eloquent\Schemer\Validation\Result\IssueRendererInterface;
use Eloquent\Schemer\Validation\Result\ValidationResult;
use Eloquent\Schemer\Value\ConcreteValueInterface;
use Exception;

final class InvalidSchemaException extends Exception
{
    /**
     * @param ConcreteValueInterface      $value
     * @param ValidationResult            $result
     * @param Exception|null              $previous
     * @param IssueRendererInterface|null $issueRenderer
     */
    public function __construct(
        ConcreteValueInterface $value,
        ValidationResult $result,
        Exception $previous = null,
        IssueRendererInterface $issueRenderer = null
    ) {
        if (null === $issueRenderer) {
            $issueRenderer = new IssueRenderer;
        }

        $this->value = $value;
        $this->result = $result;
        $this->issueRenderer = $issueRenderer;

        parent::__construct(
            sprintf(
                "Invalid schema:\n%s",
                $issueRenderer->renderManyString($result->issues())
            ),
            0,
            $previous
        );
    }

    /**
     * @return ConcreteValueInterface
     */
    public function value()
    {
        return $this->value;
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

    private $value;
    private $result;
    private $issueRenderer;
}
