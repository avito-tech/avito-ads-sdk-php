<?php

declare(strict_types=1);

namespace AvitoAds\Resource;

use AvitoAds\Model\Creative;
use AvitoAds\Model\Filter\AbstractFilter;
use AvitoAds\Model\Pagination\ListRequest;
use AvitoAds\Model\Pagination\PaginatedResult;
use AvitoAds\Model\Pagination\ResultIterator;

/**
 * Методы работы с креативами.
 */
final class CreativeResource extends AbstractResource
{
    /**
     * Список креативов (постранично).
     *
     * POST /v1/account/{accountID}/creatives
     *
     * @param array<string, mixed>|AbstractFilter|null $filter
     * @return PaginatedResult<Creative>
     */
    public function list($filter = null, int $limit = ListRequest::DEFAULT_LIMIT, int $page = 1): PaginatedResult
    {
        $request = $this->listRequest($filter, $limit, $page);
        $response = $this->http->request('POST', $this->accountPath('creatives'), $request->toArray());

        return $this->paginate($response, 'creatives', $request, static function (array $row): Creative {
            return Creative::fromArray($row);
        });
    }

    /**
     * Перебор всех креативов по всем страницам.
     *
     * @param array<string, mixed>|AbstractFilter|null $filter
     * @return \Generator<int, Creative>
     */
    public function iterate($filter = null, int $limit = ListRequest::DEFAULT_LIMIT): \Generator
    {
        $request = $this->listRequest($filter, $limit, 1);

        return ResultIterator::iterate(function (ListRequest $req): PaginatedResult {
            $response = $this->http->request('POST', $this->accountPath('creatives'), $req->toArray());

            return $this->paginate($response, 'creatives', $req, static function (array $row): Creative {
                return Creative::fromArray($row);
            });
        }, $request);
    }
}
