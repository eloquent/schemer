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

use Buzz\Browser;
use Eloquent\Schemer\Serialization\SerializationProtocolInterface;
use Eloquent\Schemer\Value\Transform\ValueTransform;
use Eloquent\Schemer\Value\Transform\ValueTransformInterface;
use Icecave\Isolator\Isolator;
use InvalidArgumentException;
use RuntimeException;
use Zend\Uri\File as FileUri;
use Zend\Uri\Http as HttpUri;
use Zend\Uri\UriInterface;

abstract class AbstractReader implements ReaderInterface
{
    /**
     * @param SerializationProtocolInterface $protocol
     * @param ValueTransformInterface|null   $transform
     * @param Browser|null                   $browser
     * @param Isolator|null                  $isolator
     */
    public function __construct(
        SerializationProtocolInterface $protocol,
        ValueTransformInterface $transform = null,
        Browser $browser = null,
        Isolator $isolator = null
    ) {
        if (null === $transform) {
            $transform = new ValueTransform;
        }
        if (null === $browser) {
            $browser = new Browser;
        }

        $this->protocol = $protocol;
        $this->transform = $transform;
        $this->browser = $browser;
        $this->isolator = Isolator::get($isolator);
    }

    /**
     * @return SerializationProtocolInterface
     */
    public function protocol()
    {
        return $this->protocol;
    }

    /**
     * @return ValueTransformInterface
     */
    public function transform()
    {
        return $this->transform;
    }

    /**
     * @return Browser
     */
    public function browser()
    {
        return $this->browser;
    }

    /**
     * @param string                   $data
     * @param UriInterface|string|null $context
     *
     * @return \Eloquent\Schemer\Value\ValueInterface
     */
    public function read($data, $context = null)
    {
        if (is_string($context)) {
            $context = UriFactory::factory($context);
        }

        try {
            $value = $this->protocol()->thaw($data);
        } catch (ThawExceptionInterface $e) {
            throw new RuntimeException(
                sprintf("Error parsing data from '%s'.", $context->toString()),
                0,
                $e
            );
        }

        return $this->transform()->apply($value);
    }

    /**
     * @param FileUri|string $path
     *
     * @return \Eloquent\Schemer\Value\ValueInterface
     */
    public function readFile($path)
    {
        if (is_string($path)) {
            if ($this->isolator->defined('PHP_WINDOWS_VERSION_BUILD')) {
                $path = FileUri::fromWindowsPath($path);
            } else {
                $path = FileUri::fromUnixPath($path);
            }
        } elseif (!$path instanceof FileUri) {
            throw new InvalidArgumentException(
                'Path must be a file URI object or a string.'
            );
        }

        try {
            $data = $this->isolator->file_get_contents($path->toString());
        } catch (ErrorException $e) {
            throw new RuntimeException(
                sprintf("Unable to read from '%s'.", $path->toString()),
                0,
                $e
            );
        }

        return $this->read($data, $path);
    }

    /**
     * @param HttpUri|string $uri
     *
     * @return \Eloquent\Schemer\Value\ValueInterface
     */
    public function readHttp($uri)
    {
        if (is_string($uri)) {
            $uri = UriFactory::factory($uri);
            if (
                !$uri instanceof HttpUri ||
                !$uri->isAbsolute()
            ) {
                throw new InvalidArgumentException(
                    'URI must be an absolute HTTP URI.'
                );
            }
        } elseif (!$uri instanceof HttpUri) {
            throw new InvalidArgumentException(
                'URI must be a HTTP URI object or a string.'
            );
        }

        $response = $this->browser()->get($uri->toString());
        if (!$response->isSuccessful()) {
            throw new RuntimeException(
                sprintf("Unable to read from '%s'.", $uri->toString()),
                0,
                $e
            );
        }

        return $this->read($response->getContent(), $uri);
    }

    private $protocol;
    private $transform;
    private $browser;
    private $isolator;
}
