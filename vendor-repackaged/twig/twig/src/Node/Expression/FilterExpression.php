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

namespace Dreitier\Nadi\Vendor\Twig\Node\Expression;

use Dreitier\Nadi\Vendor\Twig\Attribute\FirstClassTwigCallableReady;
use Dreitier\Nadi\Vendor\Twig\Compiler;
use Dreitier\Nadi\Vendor\Twig\Node\NameDeprecation;
use Dreitier\Nadi\Vendor\Twig\Node\Node;
use Dreitier\Nadi\Vendor\Twig\TwigFilter;

class FilterExpression extends CallExpression
{
    #[FirstClassTwigCallableReady]
    public function __construct(Node $node, TwigFilter|ConstantExpression $filter, Node $arguments, int $lineno)
    {
        if ($filter instanceof TwigFilter) {
            $name = $filter->getName();
            $filterName = new ConstantExpression($name, $lineno);
        } else {
            $name = $filter->getAttribute('value');
            $filterName = $filter;
            trigger_deprecation('twig/twig', '3.12', 'Not passing an instance of "TwigFilter" when creating a "%s" filter of type "%s" is deprecated.', $name, static::class);
        }

        parent::__construct(['node' => $node, 'filter' => $filterName, 'arguments' => $arguments], ['name' => $name, 'type' => 'filter'], $lineno);

        if ($filter instanceof TwigFilter) {
            $this->setAttribute('dreitier_nadi__twig_callable', $filter);
        }

        $this->deprecateNode('filter', new NameDeprecation('twig/twig', '3.12'));

        $this->deprecateAttribute('needs_charset', new NameDeprecation('twig/twig', '3.12'));
        $this->deprecateAttribute('needs_environment', new NameDeprecation('twig/twig', '3.12'));
        $this->deprecateAttribute('needs_context', new NameDeprecation('twig/twig', '3.12'));
        $this->deprecateAttribute('arguments', new NameDeprecation('twig/twig', '3.12'));
        $this->deprecateAttribute('callable', new NameDeprecation('twig/twig', '3.12'));
        $this->deprecateAttribute('is_variadic', new NameDeprecation('twig/twig', '3.12'));
        $this->deprecateAttribute('dynamic_name', new NameDeprecation('twig/twig', '3.12'));
    }

    public function compile(Compiler $compiler): void
    {
        $name = $this->getNode('filter', false)->getAttribute('value');
        if ($name !== $this->getAttribute('name')) {
            trigger_deprecation('twig/twig', '3.11', 'Changing the value of a "filter" node in a NodeVisitor class is not supported anymore.');
            $this->removeAttribute('dreitier_nadi__twig_callable');
        }
        if ('raw' === $name) {
            trigger_deprecation('twig/twig', '3.11', 'Creating the "raw" filter via "FilterExpression" is deprecated; use "RawFilter" instead.');

            $compiler->subcompile($this->getNode('node'));

            return;
        }

        if (!$this->hasAttribute('dreitier_nadi__twig_callable')) {
            $this->setAttribute('dreitier_nadi__twig_callable', $compiler->getEnvironment()->getFilter($name));
        }

        $this->compileCallable($compiler);
    }
}
