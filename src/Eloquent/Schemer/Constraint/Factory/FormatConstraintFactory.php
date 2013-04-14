<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2013 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Schemer\Constraint\Factory;

class FormatConstraintFactory implements FormatConstraintFactoryInterface
{
    /**
     * @param array<string,string> $classMap
     */
    public function __construct(array $classMap = null)
    {
        if (null === $classMap) {
            $classMap = $this->defaultClassMap();
        }

        $this->classMap = $classMap;
    }

    /**
     * @return array<string,string>
     */
    public function classMap()
    {
        return $this->classMap;
    }

    /**
     * @return array<string,string>
     */
    public function defaultClassMap()
    {
        return array(
            'date-time' => 'Eloquent\Schemer\Constraint\StringValue\DateTimeFormatConstraint',
            'email'     => 'Eloquent\Schemer\Constraint\StringValue\EmailFormatConstraint',
            'hostname'  => 'Eloquent\Schemer\Constraint\StringValue\HostnameFormatConstraint',
            'ipv4'      => 'Eloquent\Schemer\Constraint\StringValue\Ipv4AddressFormatConstraint',
            'ipv6'      => 'Eloquent\Schemer\Constraint\StringValue\Ipv6AddressFormatConstraint',
            'uri'       => 'Eloquent\Schemer\Constraint\StringValue\UriFormatConstraint',
        );
    }

    /**
     * @param string $key
     * @param string $class
     */
    public function set($key, $class)
    {
        $this->classMap[$key] = $class;
    }

    /**
     * @param string $key
     *
     * @return \Eloquent\Schemer\Constraint\FormatConstraintInterface|null
     */
    public function create($key)
    {
        $classMap = $this->classMap();
        if (array_key_exists($key, $classMap)) {
            $class = $classMap[$key];

            return new $class;
        }

        return null;
    }

    private $classMap;
}
