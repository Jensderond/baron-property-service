<?php

namespace App\Pagination;

use ApiPlatform\State\Pagination\PaginatorInterface;
use IteratorAggregate;
use ArrayIterator;
use Traversable;

class OfferPaginator implements PaginatorInterface, IteratorAggregate {
    private $items;
    private float $totalItems;
    private float $lastPage;
    private float $currentPage;
    private float $itemsPerPage;

    public function __construct(array $items, int $totalItems, int $currentPage, int $itemsPerPage) {
        $this->items = $items;
        $this->totalItems = $totalItems;
        $this->currentPage = $currentPage;
        $this->itemsPerPage = $itemsPerPage;
        $this->lastPage = (int) ceil($totalItems / $itemsPerPage);
    }

    public function getCurrentPage(): float {
        return $this->currentPage;
    }

    public function getLastPage(): float {
        return $this->lastPage;
    }

    public function getTotalItems(): float {
        return $this->totalItems;
    }

    public function getItemsPerPage(): float {
        return $this->itemsPerPage;
    }

    public function count(): int {
        return count($this->items);
    }

    public function getIterator(): Traversable {
        return new ArrayIterator($this->items);
    }
}
