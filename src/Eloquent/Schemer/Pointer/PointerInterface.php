<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2013 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Schemer\Pointer;

interface PointerInterface
{
    /**
     * @return array<string>
     */
    public function atoms();

    /**
     * @return boolean
     */
    public function hasAtoms();

    /**
     * @param string $atom
     *
     * @return PointerInterface
     */
    public function joinAtom($atom);

    /**
     * @return PointerInterface
     */
    public function parent($atom);

    /**
     * @return string
     */
    public function string();
}
