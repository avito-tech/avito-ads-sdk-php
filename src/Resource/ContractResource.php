<?php

declare(strict_types=1);

namespace AvitoAds\Resource;

use AvitoAds\Model\Contract;
use AvitoAds\Model\CreatedContract;
use AvitoAds\Model\Filter\AbstractFilter;
use AvitoAds\Model\Pagination\ListRequest;
use AvitoAds\Model\Pagination\PaginatedResult;
use AvitoAds\Model\Pagination\ResultIterator;

/**
 * Методы работы с договорами.
 */
final class ContractResource extends AbstractResource
{
    /**
     * Создаёт договор.
     *
     * POST /v1/account/{accountID}/create-contract
     *
     * Принимает ContractBuilder (с проверкой правил по типу) либо готовый массив тела.
     *
     * @param ContractBuilder|array<string, mixed> $contract
     */
    public function create($contract): CreatedContract
    {
        $body = $contract instanceof ContractBuilder ? $contract->build() : $contract;
        $response = $this->http->request('POST', $this->accountPath('create-contract'), $body);

        return CreatedContract::fromArray($response->getData());
    }

    /**
     * Список договоров (постранично).
     *
     * POST /v1/account/{accountID}/contracts
     *
     * @param array<string, mixed>|AbstractFilter|null $filter
     * @return PaginatedResult<Contract>
     */
    public function list($filter = null, int $limit = ListRequest::DEFAULT_LIMIT, int $page = 1): PaginatedResult
    {
        $request = $this->listRequest($filter, $limit, $page);
        $response = $this->http->request('POST', $this->accountPath('contracts'), $request->toArray());

        return $this->paginate($response, 'contracts', $request, static function (array $row): Contract {
            return Contract::fromArray($row);
        });
    }

    /**
     * Перебор всех договоров по всем страницам.
     *
     * @param array<string, mixed>|AbstractFilter|null $filter
     * @return \Generator<int, Contract>
     */
    public function iterate($filter = null, int $limit = ListRequest::DEFAULT_LIMIT): \Generator
    {
        $request = $this->listRequest($filter, $limit, 1);

        return ResultIterator::iterate(function (ListRequest $req): PaginatedResult {
            $response = $this->http->request('POST', $this->accountPath('contracts'), $req->toArray());

            return $this->paginate($response, 'contracts', $req, static function (array $row): Contract {
                return Contract::fromArray($row);
            });
        }, $request);
    }
}
