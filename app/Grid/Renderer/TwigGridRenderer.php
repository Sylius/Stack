<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Grid\Renderer;

use Sylius\Bundle\ResourceBundle\Grid\Parser\OptionsParserInterface;
use Sylius\Component\Grid\Definition\Action;
use Sylius\Component\Grid\Definition\Field;
use Sylius\Component\Grid\Definition\Filter;
use Sylius\Component\Grid\Renderer\GridRendererInterface;
use Sylius\Component\Grid\View\GridViewInterface;
use Symfony\Component\DependencyInjection\Attribute\AsDecorator;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Sylius\Bundle\GridBundle\Renderer\TwigGridRenderer as SyliusTwigGridRenderer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Environment;

#[AsDecorator(decorates: 'sylius.custom_grid_renderer.twig')]
final class TwigGridRenderer implements GridRendererInterface
{
    public function __construct(
        private SyliusTwigGridRenderer $gridRenderer,
        private Environment $twig,
        private OptionsParserInterface $optionsParser,
        private RequestStack $requestStack,
        #[Autowire(param: 'sylius.grid.templates.action')]
        private array $actionTemplates = [],
    ) {
    }

    public function render(GridViewInterface $gridView, ?string $template = null)
    {
        return $this->gridRenderer->render($gridView, $template);
    }

    public function renderField(GridViewInterface $gridView, Field $field, $data)
    {
        return $this->gridRenderer->renderField($gridView, $field, $data);
    }

    public function renderAction(GridViewInterface $gridView, Action $action, $data = null)
    {
        $type = $action->getType();
        if (!isset($this->actionTemplates[$type])) {
            throw new \InvalidArgumentException(sprintf('Missing template for action type "%s".', $type));
        }

        $options = $this->optionsParser->parseOptions(
            $action->getOptions(),
            $this->requestStack->getCurrentRequest() ?? new Request(),
            $data,
        );

        return $this->twig->render($this->actionTemplates[$type], [
            'grid' => $gridView,
            'action' => $action,
            'data' => $data,
            'options' => $options,
        ]);
    }

    public function renderFilter(GridViewInterface $gridView, Filter $filter)
    {
        return $this->gridRenderer->renderFilter($gridView, $filter);
    }
}
