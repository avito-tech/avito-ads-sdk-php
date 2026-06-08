<?php

declare(strict_types=1);

namespace AvitoAds\Model\Pagination;

use AvitoAds\Exception\ValidationException;

/**
 * Параметры пагинации для list-методов.
 *
 * Единый формат запроса: {filter, limit, page}. Ограничения по документации:
 * limit — 1–100 (по умолчанию 20), page — от 1 (по умолчанию 1).
 */
final class ListRequest
{
    public const MAX_LIMIT = 100;
    public const MIN_LIMIT = 1;
    public const DEFAULT_LIMIT = 20;

    /** @var array<string, mixed> */
    private $filter;

    /** @var int */
    private $limit;

    /** @var int */
    private $page;

    /**
     * @param array<string, mixed> $filter
     */
    public function __construct(array $filter = [], int $limit = self::DEFAULT_LIMIT, int $page = 1)
    {
        if ($limit < self::MIN_LIMIT || $limit > self::MAX_LIMIT) {
            throw new ValidationException(sprintf(
                'limit должен быть в диапазоне %d–%d, передано %d.',
                self::MIN_LIMIT,
                self::MAX_LIMIT,
                $limit
            ));
        }
        if ($page < 1) {
            throw new ValidationException(sprintf('page должен быть ≥ 1, передано %d.', $page));
        }

        $this->filter = $filter;
        $this->limit = $limit;
        $this->page = $page;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function getPage(): int
    {
        return $this->page;
    }

    /**
     * @return array<string, mixed>
     */
    public function getFilter(): array
    {
        return $this->filter;
    }

    /**
     * Возвращает копию запроса с указанной страницей.
     */
    public function withPage(int $page): self
    {
        return new self($this->filter, $this->limit, $page);
    }

    /**
     * Тело запроса для отправки в API.
     *
     * Пустой фильтр сериализуется как JSON-объект `{}`, а не как массив `[]`,
     * как того требует API.
     *
     * @return array{filter: array<string, mixed>|\stdClass, limit: int, page: int}
     */
    public function toArray(): array
    {
        return [
            'filter' => $this->filter === [] ? new \stdClass() : $this->filter,
            'limit' => $this->limit,
            'page' => $this->page,
        ];
    }
}
