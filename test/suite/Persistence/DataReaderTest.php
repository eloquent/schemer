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

use Eloquent\Liberator\Liberator;
use Eloquent\Schemer\Mime\PathTypeMapper;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Message\Response;
use GuzzleHttp\Stream\Stream;
use Icecave\Isolator\Isolator;
use PHPUnit_Framework_TestCase;
use Phake;

class DataReaderTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->pathTypeMapper = new PathTypeMapper;
        $this->httpClient = Phake::mock('GuzzleHttp\Client');
        $this->isolator = Phake::mock(Isolator::className());
        $this->reader = new DataReader($this->pathTypeMapper, $this->httpClient, $this->isolator);

        $this->httpUri = 'http://example.org/path/to/file.js%6Fn';
        $this->httpsUri = 'https://example.org/path/to/file.js%6Fn';
        $this->fileUri = 'file:///path/to/file.js%6Fn';
        $this->path = '/path/to/file.json';
        $this->data = 'data';
        $this->mimeType = 'mime/type';
        $this->jsonMimeType = 'application/json';
        $this->stream = fopen('php://memory', 'rb');

        $this->httpResponseBody = Stream::factory($this->data);
        $this->httpSuccessResponse =
            new Response(200, ['content-type' => $this->jsonMimeType], $this->httpResponseBody);
        $this->httpFailureResponse = new Response(444, [], null, ['reason_phrase' => 'You done goofed']);
    }

    protected function tearDown()
    {
        @fclose($this->stream);
        $this->httpResponseBody->close();
    }

    public function testConstructor()
    {
        $this->assertSame($this->pathTypeMapper, $this->reader->pathTypeMapper());
        $this->assertSame($this->httpClient, $this->reader->httpClient());
    }

    public function testConstructorDefaults()
    {
        $this->reader = new DataReader;

        $this->assertSame(PathTypeMapper::instance(), $this->reader->pathTypeMapper());
        $this->assertEquals(new Client, $this->reader->httpClient());
    }

    public function testReadFromUriWithHttpUri()
    {
        Phake::when($this->httpClient)->get($this->httpUri)->thenReturn($this->httpSuccessResponse);
        $expected = new DataPacket($this->data, $this->mimeType);
        $actual = $this->reader->readFromUri($this->httpUri, $this->mimeType);

        $this->assertEquals($expected, $actual);
    }

    public function testReadFromUriWithHttpsUri()
    {
        Phake::when($this->httpClient)->get($this->httpsUri)->thenReturn($this->httpSuccessResponse);
        $expected = new DataPacket($this->data, $this->mimeType);
        $actual = $this->reader->readFromUri($this->httpsUri, $this->mimeType);

        $this->assertEquals($expected, $actual);
    }

    public function testReadFromUriWithFileUri()
    {
        Phake::when($this->isolator)->file_get_contents($this->path)->thenReturn($this->data);
        $expected = new DataPacket($this->data, $this->mimeType);
        $actual = $this->reader->readFromUri($this->fileUri, $this->mimeType);

        $this->assertEquals($expected, $actual);
    }

    public function testReadFromUriWithAmbiguousUri()
    {
        Phake::when($this->isolator)->file_get_contents($this->path)->thenReturn($this->data);
        $expected = new DataPacket($this->data, $this->mimeType);
        $actual = $this->reader->readFromUri($this->path, $this->mimeType);

        $this->assertEquals($expected, $actual);
    }

    public function testReadFromUriWithNoPathUri()
    {
        Phake::when($this->isolator)->file_get_contents('')->thenReturn($this->data);
        $expected = new DataPacket($this->data, $this->mimeType);
        $actual = $this->reader->readFromUri('file://host', $this->mimeType);

        $this->assertEquals($expected, $actual);
    }

    public function testReadFromUriFailureInvalidUri()
    {
        $exception = null;
        try {
            $exception = $this->reader->readFromUri('scheme://');
        } catch (Exception $exception) {}

        $this->assertInstanceOf('Eloquent\Schemer\Persistence\Exception\ReadException', $exception);
        $this->assertInstanceOf(
            'Eloquent\Schemer\Persistence\Exception\InvalidUriException',
            $exception->getPrevious()
        );
        $this->assertSame("Invalid URI 'scheme://'.", $exception->getPrevious()->getMessage());
    }

    public function testReadFromUriFailureUnsupportedScheme()
    {
        $exception = null;
        try {
            $exception = $this->reader->readFromUri('scheme://host');
        } catch (Exception $exception) {}

        $this->assertInstanceOf('Eloquent\Schemer\Persistence\Exception\ReadException', $exception);
        $this->assertInstanceOf(
            'Eloquent\Schemer\Persistence\Exception\UnsupportedUriSchemeException',
            $exception->getPrevious()
        );
        $this->assertSame("Unsupported URI scheme 'scheme'.", $exception->getPrevious()->getMessage());
    }

    public function testReadFromHttp()
    {
        Phake::when($this->httpClient)->get($this->httpUri)->thenReturn($this->httpSuccessResponse);
        $expected = new DataPacket($this->data, $this->mimeType);
        $actual = $this->reader->readFromHttp($this->httpUri, $this->mimeType);

        $this->assertEquals($expected, $actual);
    }

    public function testReadFromHttpWithRequestMimeType()
    {
        Phake::when($this->httpClient)->get($this->httpUri)->thenReturn($this->httpSuccessResponse);
        $expected = new DataPacket($this->data, $this->jsonMimeType);
        $actual = $this->reader->readFromHttp($this->httpUri);

        $this->assertEquals($expected, $actual);
    }

    public function testReadFromHttpWithNoMimeType()
    {
        $this->httpSuccessResponse->setHeader('content-type', []);
        Phake::when($this->httpClient)->get($this->httpUri)->thenReturn($this->httpSuccessResponse);
        $expected = new DataPacket($this->data, $this->jsonMimeType);
        $actual = $this->reader->readFromHttp($this->httpUri);

        $this->assertEquals($expected, $actual);
    }

    public function testReadFromHttpFailureUnexpectedResponse()
    {
        Phake::when($this->httpClient)->get($this->httpUri)->thenReturn($this->httpFailureResponse);
        $exception = null;
        try {
            $exception = $this->reader->readFromHttp($this->httpUri);
        } catch (Exception $exception) {}

        $this->assertInstanceOf('Eloquent\Schemer\Persistence\Exception\ReadException', $exception);
        $this->assertInstanceOf(
            'Eloquent\Schemer\Persistence\Exception\UnexpectedHttpResponseException',
            $exception->getPrevious()
        );
        $this->assertSame(
            "Unexpected HTTP response: 'You done goofed' (444).",
            $exception->getPrevious()->getMessage()
        );
    }

    public function testReadFromHttpFailureRequestFailure()
    {
        $requestException = Phake::mock('GuzzleHttp\Exception\RequestException');
        Phake::when($this->httpClient)->get($this->httpUri)->thenThrow($requestException);
        $exception = null;
        try {
            $exception = $this->reader->readFromHttp($this->httpUri);
        } catch (Exception $exception) {}

        $this->assertInstanceOf('Eloquent\Schemer\Persistence\Exception\ReadException', $exception);
        $this->assertSame($requestException, $exception->getPrevious());
    }

    public function testReadFromFile()
    {
        Phake::when($this->isolator)->file_get_contents($this->path)->thenReturn($this->data);
        $expected = new DataPacket($this->data, $this->mimeType);
        $actual = $this->reader->readFromFile($this->path, $this->mimeType);

        $this->assertEquals($expected, $actual);
    }

    public function testReadFromFileWithNoMimeType()
    {
        Phake::when($this->isolator)->file_get_contents($this->path)->thenReturn($this->data);
        $expected = new DataPacket($this->data, $this->jsonMimeType);
        $actual = $this->reader->readFromFile($this->path);

        $this->assertEquals($expected, $actual);
    }

    public function testReadFromFileFailure()
    {
        Phake::when($this->isolator)->file_get_contents($this->path)->thenReturn(false);
        Phake::when($this->isolator)->error_get_last()
            ->thenReturn(['message' => 'message', 'type' => E_ERROR, 'file' => '/path/to/file.php', 'line' => 111]);
        $exception = null;
        try {
            $this->reader->readFromFile($this->path);
        } catch (Exception $exception) {}

        $this->assertInstanceOf('Eloquent\Schemer\Persistence\Exception\ReadException', $exception);
        $this->assertInstanceOf('ErrorException', $exception->getPrevious());
        $this->assertSame('message', $exception->getPrevious()->getMessage());
        $this->assertSame(0, $exception->getPrevious()->getCode());
        $this->assertSame('/path/to/file.php', $exception->getPrevious()->getFile());
        $this->assertSame(111, $exception->getPrevious()->getLine());
    }

    public function testReadFromFileFailureWithNoError()
    {
        Phake::when($this->isolator)->file_get_contents($this->path)->thenReturn(false);
        $exception = null;
        try {
            $this->reader->readFromFile($this->path);
        } catch (Exception $exception) {}

        $this->assertInstanceOf('Eloquent\Schemer\Persistence\Exception\ReadException', $exception);
        $this->assertNull($exception->getPrevious());
    }

    public function testReadFromStream()
    {
        Phake::when($this->isolator)->stream_get_contents($this->stream)->thenReturn($this->data);
        $expected = new DataPacket($this->data, $this->mimeType);
        $actual = $this->reader->readFromStream($this->stream, $this->mimeType);

        $this->assertEquals($expected, $actual);
    }

    public function testReadFromStreamWithNoMimeType()
    {
        Phake::when($this->isolator)->stream_get_contents($this->stream)->thenReturn($this->data);
        $expected = new DataPacket($this->data, $this->jsonMimeType);
        $actual = $this->reader->readFromStream($this->stream, null, $this->httpUri);

        $this->assertEquals($expected, $actual);
    }

    public function testReadFromStreamFailure()
    {
        Phake::when($this->isolator)->stream_get_contents($this->stream)->thenReturn(false);
        Phake::when($this->isolator)->error_get_last()
            ->thenReturn(['message' => 'message', 'type' => E_ERROR, 'file' => '/path/to/file.php', 'line' => 111]);
        $exception = null;
        try {
            $this->reader->readFromStream($this->stream);
        } catch (Exception $exception) {}

        $this->assertInstanceOf('Eloquent\Schemer\Persistence\Exception\ReadException', $exception);
        $this->assertInstanceOf('ErrorException', $exception->getPrevious());
        $this->assertSame('message', $exception->getPrevious()->getMessage());
        $this->assertSame(0, $exception->getPrevious()->getCode());
        $this->assertSame('/path/to/file.php', $exception->getPrevious()->getFile());
        $this->assertSame(111, $exception->getPrevious()->getLine());
    }

    public function testReadFromStreamFailureWithNoError()
    {
        Phake::when($this->isolator)->stream_get_contents($this->stream)->thenReturn(false);
        $exception = null;
        try {
            $this->reader->readFromStream($this->stream);
        } catch (Exception $exception) {}

        $this->assertInstanceOf('Eloquent\Schemer\Persistence\Exception\ReadException', $exception);
        $this->assertNull($exception->getPrevious());
    }

    public function testInstance()
    {
        $class = get_class($this->reader);
        Liberator::liberateClass($class)->instance = null;
        $actual = $class::instance();

        $this->assertInstanceOf($class, $actual);
        $this->assertSame($actual, $class::instance());
    }
}
