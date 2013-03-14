<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2013 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Schemer\Loader;

use Icecave\Isolator\Isolator;
use InvalidArgumentException;
use Zend\Uri\File as FileUri;

class FileSystemLoader implements LoaderInterface
{
    /**
     * @param Isolator|null $isolator
     */
    public function __construct(Isolator $isolator = null)
    {
        $this->isolator = Isolator::get($isolator);
    }

    /**
     * @param UriInterface $uri
     *
     * @return Content
     */
    public function load(UriInterface $uri)
    {
        if (!$uri instanceof FileUri) {
            throw new InvalidArgumentException('URI must be a file URI.');
        }
    }

    protected function

    private $isolator;
}
