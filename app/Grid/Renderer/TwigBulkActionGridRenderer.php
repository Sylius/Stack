<?php

declare(strict_types=1);

namespace App\Grid\Renderer;

use Sylius\Bundle\ResourceBundle\Grid\Parser\OptionsParserInterface;
use Sylius\Component\Grid\Definition\Action;
use Sylius\Component\Grid\Renderer\BulkActionGridRendererInterface;
use Sylius\Component\Grid\View\GridViewInterface;
use Symfony\Component\DependencyInjection\Attribute\AsDecorator;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Environment;

#[AsDecorator(decorates: 'sylius.custom_bulk_action_grid_renderer.twig')]
final class TwigBulkActionGridRenderer implements BulkActionGridRendererInterface
{
    public function __construct(
        private readonly Environment $twig,
        private readonly OptionsParserInterface $optionsParser,
        #[Autowire(param: 'sylius.grid.templates.bulk_action')]
        private array $bulkActionTemplates = [],
        private readonly RequestStack|null $requestStack = null,
    ) {
    }

    public function renderBulkAction(GridViewInterface $gridView, Action $bulkAction, $data = null): string
    {
        $type = $bulkAction->getType();
        if (!isset($this->bulkActionTemplates[$type])) {
            throw new \InvalidArgumentException(sprintf('Missing template for bulk action type "%s".', $type));
        }

        $options = $this->optionsParser->parseOptions(
            $bulkAction->getOptions(),
            $this->requestStack?->getCurrentRequest() ?? new Request(),
            $data,
        );

        return $this->twig->render($this->bulkActionTemplates[$type], [
            'grid' => $gridView,
            'action' => $bulkAction,
            'data' => $data,
            'options' => $options,
        ]);
    }
}
