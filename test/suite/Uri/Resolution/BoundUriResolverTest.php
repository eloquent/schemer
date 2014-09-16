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

use Exception;
use PHPUnit_Framework_TestCase;

class BoundUriResolverTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->baseUri = 'scheme://host';
        $this->resolver = BoundUriResolver::fromUriString($this->baseUri);
    }

    public function testConstructor()
    {
        $this->assertSame($this->baseUri, $this->resolver->baseUri());
    }

    public function resolveData()
    {
        //                                          baseUri               uri        resolvedUri
        return [
            'RFC 3986 section 5.4.1 example 1'  => ['http://a/b/c/d;p?q', 'g:h',     'g:h'],
            'RFC 3986 section 5.4.1 example 2'  => ['http://a/b/c/d;p?q', 'g',       'http://a/b/c/g'],
            'RFC 3986 section 5.4.1 example 3'  => ['http://a/b/c/d;p?q', './g',     'http://a/b/c/g'],
            'RFC 3986 section 5.4.1 example 4'  => ['http://a/b/c/d;p?q', 'g/',      'http://a/b/c/g/'],
            'RFC 3986 section 5.4.1 example 5'  => ['http://a/b/c/d;p?q', '/g',      'http://a/g'],
            'RFC 3986 section 5.4.1 example 6'  => ['http://a/b/c/d;p?q', '//g',     'http://g'],
            'RFC 3986 section 5.4.1 example 7'  => ['http://a/b/c/d;p?q', '?y',      'http://a/b/c/d;p?y'],
            'RFC 3986 section 5.4.1 example 8'  => ['http://a/b/c/d;p?q', 'g?y',     'http://a/b/c/g?y'],
            'RFC 3986 section 5.4.1 example 9'  => ['http://a/b/c/d;p?q', '#s',      'http://a/b/c/d;p?q#s'],
            'RFC 3986 section 5.4.1 example 10' => ['http://a/b/c/d;p?q', 'g#s',     'http://a/b/c/g#s'],
            'RFC 3986 section 5.4.1 example 11' => ['http://a/b/c/d;p?q', 'g?y#s',   'http://a/b/c/g?y#s'],
            'RFC 3986 section 5.4.1 example 12' => ['http://a/b/c/d;p?q', ';x',      'http://a/b/c/;x'],
            'RFC 3986 section 5.4.1 example 13' => ['http://a/b/c/d;p?q', 'g;x',     'http://a/b/c/g;x'],
            'RFC 3986 section 5.4.1 example 14' => ['http://a/b/c/d;p?q', 'g;x?y#s', 'http://a/b/c/g;x?y#s'],
            'RFC 3986 section 5.4.1 example 15' => ['http://a/b/c/d;p?q', '',        'http://a/b/c/d;p?q'],
            'RFC 3986 section 5.4.1 example 16' => ['http://a/b/c/d;p?q', '.',       'http://a/b/c/'],
            'RFC 3986 section 5.4.1 example 17' => ['http://a/b/c/d;p?q', './',      'http://a/b/c/'],
            'RFC 3986 section 5.4.1 example 18' => ['http://a/b/c/d;p?q', '..',      'http://a/b/'],
            'RFC 3986 section 5.4.1 example 19' => ['http://a/b/c/d;p?q', '../',     'http://a/b/'],
            'RFC 3986 section 5.4.1 example 20' => ['http://a/b/c/d;p?q', '../g',    'http://a/b/g'],
            'RFC 3986 section 5.4.1 example 21' => ['http://a/b/c/d;p?q', '../..',   'http://a/'],
            'RFC 3986 section 5.4.1 example 22' => ['http://a/b/c/d;p?q', '../../',  'http://a/'],
            'RFC 3986 section 5.4.1 example 23' => ['http://a/b/c/d;p?q', '../../g', 'http://a/g'],
        ];
    }

    /**
     * @dataProvider resolveData
     */
    public function testResolve($baseUri, $uri, $resolvedUri)
    {
        $resolver = BoundUriResolver::fromUriString($baseUri);

        $this->assertSame($resolvedUri, $resolver->resolve($uri));
    }

    public function testResolveFailureInvalidUri()
    {
        $resolver = BoundUriResolver::fromUriString('scheme://host');
        $exception = null;
        try {
            $resolver->resolve('scheme://');
        } catch (Exception $exception) {}

        $this->assertInstanceOf('Eloquent\Schemer\Uri\Resolution\Exception\UriResolutionException', $exception);
        $this->assertInstanceOf('Eloquent\Schemer\Uri\Exception\InvalidUriException', $exception->getPrevious());
        $this->assertSame("Invalid URI 'scheme://'.", $exception->getPrevious()->getMessage());
    }

    public function testFromUriStringFailureInvalidBaseUri()
    {
        $exception = null;
        try {
            $resolver = BoundUriResolver::fromUriString('scheme://');
        } catch (Exception $exception) {}

        $this->assertInstanceOf('Eloquent\Schemer\Uri\Exception\InvalidUriException', $exception);
    }
}
