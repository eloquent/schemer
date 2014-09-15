<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Schemer\Persistence;

use Eloquent\Schemer\Mime\PathTypeMapper;
use Eloquent\Schemer\Mime\PathTypeMapperInterface;
use Eloquent\Schemer\Persistence\Exception\InvalidUriException;
use Eloquent\Schemer\Persistence\Exception\ReadException;
use Eloquent\Schemer\Persistence\Exception\UnexpectedHttpResponseException;
use Eloquent\Schemer\Persistence\Exception\UnsupportedUriSchemeException;
use ErrorException;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\RequestException;
use Icecave\Isolator\Isolator;

/**
 * Reads data from various sources.
 */
class DataReader implements DataReaderInterface
{
    /**
     * Get a static data reader instance.
     *
     * @return DataReaderInterface The static data reader instance.
     */
    public static function instance()
    {
        if (null === self::$instance) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /**
     * Construct a new data reader.
     *
     * @param PathTypeMapperInterface|null $pathTypeMapper The path type mapper to use.
     * @param ClientInterface              $httpClient     The HTTP client to use.
     * @param Isolator|null                $isolator       The isolator to use.
     */
    public function __construct(
        PathTypeMapperInterface $pathTypeMapper = null,
        ClientInterface $httpClient = null,
        Isolator $isolator = null
    ) {
        if (null === $pathTypeMapper) {
            $pathTypeMapper = PathTypeMapper::instance();
        }
        if (null === $httpClient) {
            $httpClient = new Client;
        }

        $this->pathTypeMapper = $pathTypeMapper;
        $this->httpClient = $httpClient;
        $this->isolator = Isolator::get($isolator);
    }

    /**
     * Get the path type mapper.
     *
     * @return PathTypeMapperInterface The path type mapper.
     */
    public function pathTypeMapper()
    {
        return $this->pathTypeMapper;
    }

    /**
     * Get the HTTP client.
     *
     * @return ClientInterface The HTTP client.
     */
    public function httpClient()
    {
        return $this->httpClient;
    }

    /**
     * Read data from a URI.
     *
     * @param string      $uri      The URI.
     * @param string|null $mimeType The MIME type, if known.
     *
     * @return DataPacket    The data.
     * @throws ReadException If the data cannot be read.
     */
    public function readFromUri($uri, $mimeType = null)
    {
        $uriParts = parse_url($uri);
        if (false === $uriParts) {
            throw new ReadException($uri, new InvalidUriException($uri));
        }

        if (array_key_exists('scheme', $uriParts)) {
            $scheme = strtolower($uriParts['scheme']);
        } else {
            $scheme = 'file';
        }

        switch ($scheme) {
            case 'http':
            case 'https':
                return $this->readFromHttp($uri, $mimeType);

            case 'file':
                if (array_key_exists('path', $uriParts)) {
                    $path = rawurldecode($uriParts['path']);
                } else {
                    $path = '';
                }

                return $this->readFromFile($path, $mimeType);
        }

        throw new ReadException(
            $uri,
            new UnsupportedUriSchemeException($scheme)
        );
    }

    /**
     * Read data from a HTTP URI.
     *
     * @param string      $uri      The URI.
     * @param string|null $mimeType The MIME type, if known.
     *
     * @return DataPacket    The data.
     * @throws ReadException If the data cannot be read.
     */
    public function readFromHttp($uri, $mimeType = null)
    {
        try {
            $response = $this->httpClient()->get($uri);
        } catch (RequestException $e) {
            throw new ReadException($uri, $e);
        }

        if (200 != $response->getStatusCode()) {
            throw new ReadException(
                $uri,
                new UnexpectedHttpResponseException($response)
            );
        }

        if (null === $mimeType) {
            $responseMimeType = $response->getHeader('content-type');

            if ($responseMimeType) {
                $mimeType = $responseMimeType;
            } else {
                $mimeType = $this->pathTypeMapper()
                    ->typeByPath(rawurldecode(parse_url($uri, PHP_URL_PATH)));
            }
        }

        return new DataPacket($response->getBody()->getContents(), $mimeType);
    }

    /**
     * Read data from a file.
     *
     * @param string      $path     The path.
     * @param string|null $mimeType The MIME type, if known.
     *
     * @return DataPacket    The data.
     * @throws ReadException If the data cannot be read.
     */
    public function readFromFile($path, $mimeType = null)
    {
        $data = @$this->isolator()->file_get_contents($path);
        if (false === $data) {
            throw new ReadException($path, $this->lastError());
        }

        if (null === $mimeType) {
            $mimeType = $this->pathTypeMapper()->typeByPath($path);
        }

        return new DataPacket($data, $mimeType);
    }

    /**
     * Read data from a stream.
     *
     * @param stream      $stream   The stream.
     * @param string|null $mimeType The MIME type, if known.
     * @param string|null $uri      The URI, if known.
     *
     * @return DataPacket    The data.
     * @throws ReadException If the data cannot be read.
     */
    public function readFromStream($stream, $mimeType = null, $uri = null)
    {
        $data = @$this->isolator()->stream_get_contents($stream);
        if (false === $data) {
            throw new ReadException($uri, $this->lastError());
        }

        if (null === $mimeType) {
            $mimeType = $this->pathTypeMapper()
                ->typeByPath(rawurldecode(parse_url($uri, PHP_URL_PATH)));
        }

        return new DataPacket($data, $mimeType);
    }

    /**
     * Get the last error as an error exception.
     *
     * @return ErrorException|null The last error, or null if no error has occurred yet.
     */
    protected function lastError()
    {
        $error = $this->isolator()->error_get_last();
        if (null === $error) {
            return null;
        }

        return new ErrorException(
            $error['message'],
            0,
            $error['type'],
            $error['file'],
            $error['line']
        );
    }

    /**
     * Get the isolator.
     *
     * @return Isolator The isolator.
     */
    protected function isolator()
    {
        return $this->isolator;
    }

    private static $instance;
    private $pathTypeMapper;
    private $httpClient;
    private $isolator;
}
