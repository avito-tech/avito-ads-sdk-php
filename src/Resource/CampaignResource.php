<?php

declare(strict_types=1);

namespace AvitoAds\Resource;

use AvitoAds\Model\Campaign;
use AvitoAds\Model\Filter\AbstractFilter;
use AvitoAds\Model\Pagination\ListRequest;
use AvitoAds\Model\Pagination\PaginatedResult;
use AvitoAds\Model\Pagination\ResultIterator;

/**
 * Методы работы с рекламными кампаниями.
 */
final class CampaignResource extends AbstractResource
{
    /**
     * Список кампаний (постранично).
     *
     * POST /v1/account/{accountID}/campaigns
     *
     * @param array<string, mixed>|AbstractFilter|null $filter
     * @return PaginatedResult<Campaign>
     */
    public function list($filter = null, int $limit = ListRequest::DEFAULT_LIMIT, int $page = 1): PaginatedResult
    {
        $request = $this->listRequest($filter, $limit, $page);
        $response = $this->http->request('POST', $this->accountPath('campaigns'), $request->toArray());

        return $this->paginate($response, 'campaigns', $request, static function (array $row): Campaign {
            return Campaign::fromArray($row);
        });
    }

    /**
     * Перебор всех кампаний по всем страницам.
     *
     * @param array<string, mixed>|AbstractFilter|null $filter
     * @return \Generator<int, Campaign>
     */
    public function iterate($filter = null, int $limit = ListRequest::DEFAULT_LIMIT): \Generator
    {
        $request = $this->listRequest($filter, $limit, 1);

        return ResultIterator::iterate(function (ListRequest $req): PaginatedResult {
            $response = $this->http->request('POST', $this->accountPath('campaigns'), $req->toArray());

            return $this->paginate($response, 'campaigns', $req, static function (array $row): Campaign {
                return Campaign::fromArray($row);
            });
        }, $request);
    }
}
