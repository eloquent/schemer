<?php

/*
 * This file is part of the Schemer package.
 *
 * Copyright © 2013 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Schemer\Value\Visitor;

use Eloquent\Schemer\Pointer\Pointer;
use Eloquent\Schemer\Pointer\PointerInterface;

abstract class AbstractContextualValueVisitor implements ValueVisitorInterface
{
    public function __construct()
    {
        $this->clear();
    }

    protected function clear()
    {
        $this->setContext(new Pointer);
    }

    /**
     * @param PointerInterface $context
     */
    protected function setContext(PointerInterface $context)
    {
        $this->context = $context;
    }

    /**
     * @param string $atom
     */
    protected function pushContextAtom($atom)
    {
        $this->setContext($this->context()->joinAtom($atom));
    }

    protected function popContextAtom()
    {
        $this->setContext($this->context()->parent());
    }

    /**
     * @return PointerInterface
     */
    protected function context()
    {
        return $this->context;
    }

    private $context;
}
