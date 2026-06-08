<?php

declare(strict_types=1);

namespace AvitoAds\Model\Pagination;

/**
 * Страница результатов list-метода.
 *
 * Содержит общее число записей по фильтру (total), массив типизированных
 * сущностей текущей страницы и остаток API-баллов из заголовка ответа.
 *
 * @template T
 * @implements \IteratorAggregate<int, T>
 */
final class PaginatedResult implements \IteratorAggregate, \Countable
{
    /** @var int Общее количество записей по фильтру. */
    private $total;

    /** @var array<int, mixed> Сущности текущей страницы. */
    private $items;

    /** @var int Текущая страница. */
    private $page;

    /** @var int Размер страницы. */
    private $limit;

    /** @var int|null Остаток API-баллов. */
    private $apiPointBalance;

    /**
     * @param array<int, mixed> $items
     */
    public function __construct(int $total, array $items, int $page, int $limit, ?int $apiPointBalance = null)
    {
        $this->total = $total;
        $this->items = $items;
        $this->page = $page;
        $this->limit = $limit;
        $this->apiPointBalance = $apiPointBalance;
    }

    public function getTotal(): int
    {
        return $this->total;
    }

    /**
     * Сущности текущей страницы.
     *
     * @return array<int, mixed>
     */
    public function getItems(): array
    {
        return $this->items;
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function getApiPointBalance(): ?int
    {
        return $this->apiPointBalance;
    }

    /**
     * Есть ли следующая страница (исходя из total и текущей позиции).
     */
    public function hasNextPage(): bool
    {
        return ($this->page * $this->limit) < $this->total;
    }

    public function count(): int
    {
        return count($this->items);
    }

    /**
     * @return \ArrayIterator<int, mixed>
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->items);
    }
}
