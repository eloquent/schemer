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

use PHPUnit_Framework_TestCase;

class DataPacketTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->data = 'data';
        $this->mimeType = 'mime/type';
        $this->packet = new DataPacket($this->data, $this->mimeType);
    }

    public function testConstructor()
    {
        $this->assertSame($this->data, $this->packet->data());
        $this->assertSame($this->mimeType, $this->packet->mimeType());
    }

    public function testConstructorDefaults()
    {
        $this->packet = new DataPacket($this->data);

        $this->assertNull($this->packet->mimeType());
    }
}
