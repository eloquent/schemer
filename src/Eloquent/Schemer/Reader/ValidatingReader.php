<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2013 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Schemer\Reader;

use Eloquent\Schemer\Uri\UriFactory;
use Eloquent\Schemer\Uri\UriFactoryInterface;
use Eloquent\Schemer\Uri\UriInterface;
use Eloquent\Schemer\Validation\BoundConstraintValidator;
use Eloquent\Schemer\Validation\BoundConstraintValidatorInterface;
use Eloquent\Schemer\Validation\Exception\InvalidValueException;
use Eloquent\Schemer\Value;

class ValidatingReader extends AbstractReader
{
    /**
     * @param BoundConstraintValidatorInterface|null $validator
     * @param ReaderInterface|null                   $reader
     * @param UriFactoryInterface|null               $uriFactory
     */
    public function __construct(
        BoundConstraintValidatorInterface $validator = null,
        ReaderInterface $reader = null,
        UriFactoryInterface $uriFactory = null
    ) {
        parent::__construct($uriFactory);

        if (null === $validator) {
            $validator = new BoundConstraintValidator;
        }
        if (null === $reader) {
            $reader = new FixedScopeResolvingReader;
        }

        $this->validator = $validator;
        $this->reader = $reader;
    }

    /**
     * @return BoundConstraintValidatorInterface
     */
    public function validator()
    {
        return $this->validator;
    }

    /**
     * @return ReaderInterface
     */
    public function reader()
    {
        return $this->reader;
    }

    /**
     * @param UriInterface|string $uri
     * @param string|null         $mimeType
     *
     * @return Value\ValueInterface
     */
    public function read($uri, $mimeType = null)
    {
        $value = $this->reader()->read($uri, $mimeType);
        $result = $this->validator()->validate($value);
        if ($result->isValid()) {
            throw new InvalidValueException(
                $value,
                $this->validator()->constraint(),
                $result
            );
        }

        return $value;
    }

    private $validator;
    private $reader;
}
