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

use Eloquent\Equality\Comparator;
use PHPUnit_Framework_TestCase;
use stdClass;
use Zend\Uri\UriFactory;

class ReferenceValueTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->_innerValue = new stdClass;
        $this->_innerValue->{'$ref'} =
            new StringValue('http://example.org/path/to/foo?bar=baz#qux')
        ;
        $this->_innerValue->doom = new StringValue('splat');
        $this->_value = new ReferenceValue($this->_innerValue);

        $this->_comparator = new Comparator;
    }

    public function testConstructor()
    {
        $expectedReference = UriFactory::factory(
            'http://example.org/path/to/foo?bar=baz#qux'
        );

        $this->assertEquals($expectedReference, $this->_value->reference());
        $this->assertTrue(
            $this->_comparator->equals(
                $expectedReference,
                $this->_value->reference()
            )
        );
    }
}
