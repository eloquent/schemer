<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2013 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Schemer\Value;

use InvalidArgumentException;
use stdClass;

class ReferenceValue extends AbstractObjectValue
{
    /**
     * @param stdClass $value
     */
    public function __construct(stdClass $value)
    {
        if (
            !property_exists($value, '$ref') ||
            !$value->{'$ref'} instanceof StringValue
        ) {
            throw new InvalidArgumentException(
                'Value does not contain a valid reference.'
            );
        }

        $this->reference = $value->{'$ref'}->value();

        parent::__construct($value);
    }

    /**
     * @return string
     */
    public function reference()
    {
        return $this->reference;
    }

    /**
     * @param ValueVisitorInterface $visitor
     *
     * @return mixed
     */
    public function accept(ValueVisitorInterface $visitor)
    {
        return $visitor->visitReferenceValue($this);
    }

    private $reference;
}
