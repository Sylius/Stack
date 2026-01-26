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

namespace App\Responder;

use Pagerfanta\PagerfantaInterface;
use Port\Csv\CsvWriter;
use Sylius\Component\Grid\Definition\Field;
use Sylius\Component\Grid\Renderer\GridRendererInterface;
use Sylius\Component\Grid\View\GridViewInterface;
use Sylius\Resource\Context\Context;
use Sylius\Resource\Metadata\Operation;
use Sylius\Resource\State\ResponderInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Contracts\Translation\TranslatorInterface;
use Webmozart\Assert\Assert;

final class ExportGridToCsvResponder implements ResponderInterface
{
    public function __construct(
        #[Autowire(service: 'sylius.grid.renderer')]
        private readonly GridRendererInterface $gridRenderer,
        private readonly TranslatorInterface $translator,
    ) {
    }

    /**
     * @param GridViewInterface $data
     */
    public function respond(mixed $data, Operation $operation, Context $context): mixed
    {
        Assert::isInstanceOf($data, GridViewInterface::class);

        $response = new StreamedResponse(function () use ($data) {
            $output = fopen('php://output', 'w');

            if (false === $output) {
                throw new \RuntimeException('Unable to open output stream.');
            }

            $writer = new CsvWriter();
            $writer->setStream($output);

            $fields = $this->sortFields($data->getDefinition()->getFields());
            $this->writeHeaders($writer, $fields);
            $this->writeRows($writer, $fields, $data);

            $writer->finish();
        });

        $response->headers->set('Content-Type', 'text/csv; charset=UTF-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="export.csv"');

        return $response;
    }

    /**
     * @param Field[] $fields
     */
    private function writeHeaders(CsvWriter $writer, array $fields): void
    {
        $labels = array_map(fn (Field $field) => $this->translator->trans($field->getLabel()), $fields);

        $writer->writeItem($labels);
    }

    /**
     * @param Field[] $fields
     */
    private function writeRows(CsvWriter $writer, array $fields, GridViewInterface $gridView): void
    {
        /** @var PagerfantaInterface $paginator */
        $paginator = $gridView->getData();
        Assert::isInstanceOf($paginator, PagerfantaInterface::class);

        for ($currentPage = 1; $currentPage <= $paginator->getNbPages(); ++$currentPage) {
            $paginator->setCurrentPage($currentPage);
            $this->writePageResults($writer, $fields, $gridView, $paginator->getCurrentPageResults());
        }
    }

    /**
     * @param Field[] $fields
     * @param iterable<object> $pageResults
     */
    private function writePageResults(CsvWriter $writer, array $fields, GridViewInterface $gridView, iterable $pageResults): void
    {
        foreach ($pageResults as $resource) {
            $rows = [];
            foreach ($fields as $field) {
                $rows[] = $this->getFieldValue($gridView, $field, $resource);
            }
            $writer->writeItem($rows);
        }
    }

    private function getFieldValue(GridViewInterface $gridView, Field $field, object $data): string
    {
        $renderedData = $this->gridRenderer->renderField($gridView, $field, $data);
        $renderedData = str_replace(\PHP_EOL, '', $renderedData);

        return trim(strip_tags($renderedData));
    }

    /**
     * @param Field[] $fields
     *
     * @return Field[]
     */
    private function sortFields(array $fields): array
    {
        $sortedFields = $fields;

        uasort($sortedFields, fn (Field $fieldA, Field $fieldB) => $fieldA->getPosition() <=> $fieldB->getPosition());

        return $sortedFields;
    }
}
