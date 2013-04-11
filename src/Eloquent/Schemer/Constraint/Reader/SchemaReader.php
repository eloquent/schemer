<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2013 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Schemer\Constraint\Reader;

use Eloquent\Schemer\Constraint\Factory\SchemaFactory;
use Eloquent\Schemer\Constraint\Factory\SchemaFactoryInterface;
use Eloquent\Schemer\Reader\Reader;
use Eloquent\Schemer\Reader\ReaderInterface;

class SchemaReader implements SchemaReaderInterface
{
    /**
     * @param SchemaFactoryInterface|null $schemaFactory
     * @param ReaderInterface|null        $reader
     */
    public function __construct(
        SchemaFactoryInterface $schemaFactory = null,
        ReaderInterface $reader = null
    ) {
        if (null === $schemaFactory) {
            $schemaFactory = new SchemaFactory;
        }
        if (null === $reader) {
            $reader = new Reader;
        }

        $this->schemaFactory = $schemaFactory;
        $this->reader = $reader;
    }

    /**
     * @return SchemaFactoryInterface
     */
    public function schemaFactory()
    {
        return $this->schemaFactory;
    }

    /**
     * @return ReaderInterface
     */
    public function reader()
    {
        return $this->reader;
    }

    /**
     * @param \Zend\Uri\UriInterface|string $uri
     * @param string|null                   $mimeType
     *
     * @return \Eloquent\Schemer\Constraint\Schema
     */
    public function read($uri, $mimeType = null)
    {
        return $this->schemaFactory()->create(
            $this->reader()->read($uri, $mimeType)
        );
    }

    /**
     * @param string      $path
     * @param string|null $mimeType
     *
     * @return \Eloquent\Schemer\Constraint\Schema
     */
    public function readPath($path, $mimeType = null)
    {
        return $this->schemaFactory()->create(
            $this->reader()->readPath($path, $mimeType)
        );
    }

    /**
     * @param string      $data
     * @param string|null $mimeType
     *
     * @return \Eloquent\Schemer\Constraint\Schema
     */
    public function readString($data, $mimeType = null)
    {
        return $this->schemaFactory()->create(
            $this->reader()->readString($data, $mimeType)
        );
    }

    private $schemaFactory;
    private $reader;
}
