<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2013 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Schemer\Value;

use Countable;

interface ValueContainerInterface extends ValueInterface, Countable
{
    /**
     * @return array<integer,integer|string>
     */
    public function keys();

    /**
     * @return array<integer,ValueInterface>
     */
    public function values();

    /**
     * @param integer|string $key
     * @param ValueInterface $value
     */
    public function set($key, ValueInterface $value);

    /**
     * @param integer|string $key
     */
    public function remove($key);

    /**
     * @param integer|string $key
     *
     * @return boolean
     */
    public function has($key);

    /**
     * @param integer|string $key
     *
     * @return ValueInterface
     */
    public function get($key);

    /**
     * @param integer|string $key
     *
     * @return mixed
     */
    public function getRaw($key);

    /**
     * @param integer|string      $key
     * @param ValueInterface|null $default
     *
     * @return ValueInterface|null
     */
    public function getDefault($key, ValueInterface $default = null);

    /**
     * @param integer|string $key
     * @param mixed          $default
     *
     * @return mixed
     */
    public function getRawDefault($key, $default = null);
}
