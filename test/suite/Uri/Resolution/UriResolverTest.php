<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Schemer\Uri\Resolution;

use Eloquent\Liberator\Liberator;
use Exception;
use PHPUnit_Framework_TestCase;

class UriResolverTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->resolver = new UriResolver;
    }

    public function resolveData()
    {
        //                                          uri        baseUri               resolvedUri
        return [
            'RFC 3986 section 5.4.1 example 1'  => ['g:h',     'http://a/b/c/d;p?q', 'g:h'],
            'RFC 3986 section 5.4.1 example 2'  => ['g',       'http://a/b/c/d;p?q', 'http://a/b/c/g'],
            'RFC 3986 section 5.4.1 example 3'  => ['./g',     'http://a/b/c/d;p?q', 'http://a/b/c/g'],
            'RFC 3986 section 5.4.1 example 4'  => ['g/',      'http://a/b/c/d;p?q', 'http://a/b/c/g/'],
            'RFC 3986 section 5.4.1 example 5'  => ['/g',      'http://a/b/c/d;p?q', 'http://a/g'],
            'RFC 3986 section 5.4.1 example 6'  => ['//g',     'http://a/b/c/d;p?q', 'http://g'],
            'RFC 3986 section 5.4.1 example 7'  => ['?y',      'http://a/b/c/d;p?q', 'http://a/b/c/d;p?y'],
            'RFC 3986 section 5.4.1 example 8'  => ['g?y',     'http://a/b/c/d;p?q', 'http://a/b/c/g?y'],
            'RFC 3986 section 5.4.1 example 9'  => ['#s',      'http://a/b/c/d;p?q', 'http://a/b/c/d;p?q#s'],
            'RFC 3986 section 5.4.1 example 10' => ['g#s',     'http://a/b/c/d;p?q', 'http://a/b/c/g#s'],
            'RFC 3986 section 5.4.1 example 11' => ['g?y#s',   'http://a/b/c/d;p?q', 'http://a/b/c/g?y#s'],
            'RFC 3986 section 5.4.1 example 12' => [';x',      'http://a/b/c/d;p?q', 'http://a/b/c/;x'],
            'RFC 3986 section 5.4.1 example 13' => ['g;x',     'http://a/b/c/d;p?q', 'http://a/b/c/g;x'],
            'RFC 3986 section 5.4.1 example 14' => ['g;x?y#s', 'http://a/b/c/d;p?q', 'http://a/b/c/g;x?y#s'],
            'RFC 3986 section 5.4.1 example 15' => ['',        'http://a/b/c/d;p?q', 'http://a/b/c/d;p?q'],
            'RFC 3986 section 5.4.1 example 16' => ['.',       'http://a/b/c/d;p?q', 'http://a/b/c/'],
            'RFC 3986 section 5.4.1 example 17' => ['./',      'http://a/b/c/d;p?q', 'http://a/b/c/'],
            'RFC 3986 section 5.4.1 example 18' => ['..',      'http://a/b/c/d;p?q', 'http://a/b/'],
            'RFC 3986 section 5.4.1 example 19' => ['../',     'http://a/b/c/d;p?q', 'http://a/b/'],
            'RFC 3986 section 5.4.1 example 20' => ['../g',    'http://a/b/c/d;p?q', 'http://a/b/g'],
            'RFC 3986 section 5.4.1 example 21' => ['../..',   'http://a/b/c/d;p?q', 'http://a/'],
            'RFC 3986 section 5.4.1 example 22' => ['../../',  'http://a/b/c/d;p?q', 'http://a/'],
            'RFC 3986 section 5.4.1 example 23' => ['../../g', 'http://a/b/c/d;p?q', 'http://a/g'],
        ];
    }

    /**
     * @dataProvider resolveData
     */
    public function testResolve($uri, $baseUri, $resolvedUri)
    {
        $this->assertSame($resolvedUri, $this->resolver->resolve($uri, $baseUri));
    }

    public function testResolveFailureInvalidUri()
    {
        $exception = null;
        try {
            $this->resolver->resolve('scheme://', 'scheme://host');
        } catch (Exception $exception) {}

        $this->assertInstanceOf('Eloquent\Schemer\Uri\Resolution\Exception\UriResolutionException', $exception);
        $this->assertInstanceOf('Eloquent\Schemer\Uri\Exception\InvalidUriException', $exception->getPrevious());
        $this->assertSame("Invalid URI 'scheme://'.", $exception->getPrevious()->getMessage());
    }

    public function testResolveFailureInvalidBaseUri()
    {
        $exception = null;
        try {
            $this->resolver->resolve('/path', 'scheme://');
        } catch (Exception $exception) {}

        $this->assertInstanceOf('Eloquent\Schemer\Uri\Resolution\Exception\UriResolutionException', $exception);
        $this->assertInstanceOf('Eloquent\Schemer\Uri\Exception\InvalidUriException', $exception->getPrevious());
        $this->assertSame("Invalid URI 'scheme://'.", $exception->getPrevious()->getMessage());
    }

    public function testInstance()
    {
        $class = get_class($this->resolver);
        Liberator::liberateClass($class)->instance = null;
        $actual = $class::instance();

        $this->assertInstanceOf($class, $actual);
        $this->assertSame($actual, $class::instance());
    }
}
