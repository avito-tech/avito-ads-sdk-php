<?php

declare(strict_types=1);

namespace AvitoAds\Resource;

use AvitoAds\Http\ApiResponse;
use AvitoAds\Http\HttpClientInterface;
use AvitoAds\Model\Filter\AbstractFilter;
use AvitoAds\Model\Pagination\ListRequest;
use AvitoAds\Model\Pagination\PaginatedResult;

/**
 * Базовый класс ресурсов (групп методов API).
 *
 * Хранит транспорт и идентификатор аккаунта, собирает пути вида
 * `v1/account/{accountID}/...` и помогает разбирать постраничные ответы.
 */
abstract class AbstractResource
{
    /** @var HttpClientInterface */
    protected $http;

    /** @var int */
    protected $accountId;

    public function __construct(HttpClientInterface $http, int $accountId)
    {
        $this->http = $http;
        $this->accountId = $accountId;
    }

    /**
     * Путь относительно базового URL, начинающийся с v1/account/{accountID}.
     *
     * Без ведущего слэша, чтобы корректно склеиться с базовым URL окружения.
     */
    protected function accountPath(string $suffix = ''): string
    {
        $path = 'v1/account/' . $this->accountId;
        if ($suffix !== '') {
            $path .= '/' . ltrim($suffix, '/');
        }

        return $path;
    }

    /**
     * Строит ListRequest из фильтра, заданного массивом или объектом-фильтром.
     *
     * @param array<string, mixed>|AbstractFilter|null $filter
     */
    protected function listRequest($filter, int $limit, int $page): ListRequest
    {
        if ($filter instanceof AbstractFilter) {
            $filter = $filter->toArray();
        }

        return new ListRequest($filter ?? [], $limit, $page);
    }

    /**
     * Преобразует постраничный ответ в PaginatedResult с типизированными моделями.
     *
     * @param callable(array<string, mixed>): mixed $factory фабрика модели из строки
     * @return PaginatedResult<mixed>
     */
    protected function paginate(
        ApiResponse $response,
        string $itemsKey,
        ListRequest $request,
        callable $factory
    ): PaginatedResult {
        $data = $response->getData();
        $rows = isset($data[$itemsKey]) && is_array($data[$itemsKey]) ? $data[$itemsKey] : [];
        $items = array_map(
            static function ($row) use ($factory) {
                return $factory(is_array($row) ? $row : []);
            },
            array_values($rows)
        );

        return new PaginatedResult(
            isset($data['total']) ? (int) $data['total'] : count($items),
            $items,
            $request->getPage(),
            $request->getLimit(),
            $response->getApiPointBalance()
        );
    }
}
