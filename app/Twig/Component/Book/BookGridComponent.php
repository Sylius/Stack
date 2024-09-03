<?php

namespace App\Twig\Component\Book;

use App\Twig\Component\Grid\GridComponent;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;

#[AsLiveComponent(name: 'app_book_grid', template: 'components/grid.html.twig', csrf: false)]
class BookGridComponent extends GridComponent
{
    #[LiveProp(writable: ['type', 'value'])]
    public array $search = [];

    #[LiveAction]
    public function filter(): void
    {
        $this->currentCriteria['search'] = $this->search;
    }


}
