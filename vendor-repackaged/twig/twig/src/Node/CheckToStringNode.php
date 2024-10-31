<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Modified by __root__ on 28-October-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace Dreitier\Nadi\Vendor\Twig\Node;

use Dreitier\Nadi\Vendor\Twig\Attribute\YieldReady;
use Dreitier\Nadi\Vendor\Twig\Compiler;
use Dreitier\Nadi\Vendor\Twig\Node\Expression\AbstractExpression;

/**
 * Checks if casting an expression to __toString() is allowed by the sandbox.
 *
 * For instance, when there is a simple Print statement, like {{ article }},
 * and if the sandbox is enabled, we need to check that the __toString()
 * method is allowed if 'article' is an object. The same goes for {{ article|upper }}
 * or {{ random(article) }}
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
#[YieldReady]
class CheckToStringNode extends AbstractExpression
{
    public function __construct(AbstractExpression $expr)
    {
        parent::__construct(['expr' => $expr], [], $expr->getTemplateLine());
    }

    public function compile(Compiler $compiler): void
    {
        $expr = $this->getNode('expr');
        $compiler
            ->raw('$this->sandbox->ensureToStringAllowed(')
            ->subcompile($expr)
            ->raw(', ')
            ->repr($expr->getTemplateLine())
            ->raw(', $this->source)')
        ;
    }
}
