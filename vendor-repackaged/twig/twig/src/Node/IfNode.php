<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 * (c) Armin Ronacher
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

/**
 * Represents an if node.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
#[YieldReady]
class IfNode extends Node
{
    public function __construct(Node $tests, ?Node $else, int $lineno)
    {
        $nodes = ['tests' => $tests];
        if (null !== $else) {
            $nodes['else'] = $else;
        }

        parent::__construct($nodes, [], $lineno);
    }

    public function compile(Compiler $compiler): void
    {
        $compiler->addDebugInfo($this);
        for ($i = 0, $count = \count($this->getNode('tests')); $i < $count; $i += 2) {
            if ($i > 0) {
                $compiler
                    ->outdent()
                    ->write('} elseif (')
                ;
            } else {
                $compiler
                    ->write('if (')
                ;
            }

            $compiler
                ->subcompile($this->getNode('tests')->getNode((string) $i))
                ->raw(") {\n")
                ->indent()
            ;
            // The node might not exists if the content is empty
            if ($this->getNode('tests')->hasNode((string) ($i + 1))) {
                $compiler->subcompile($this->getNode('tests')->getNode((string) ($i + 1)));
            }
        }

        if ($this->hasNode('else')) {
            $compiler
                ->outdent()
                ->write("} else {\n")
                ->indent()
                ->subcompile($this->getNode('else'))
            ;
        }

        $compiler
            ->outdent()
            ->write("}\n");
    }
}
