<?php

declare(strict_types=1);

namespace AvitoAds\Resource;

use AvitoAds\Exception\ValidationException;
use AvitoAds\Model\Filter\AbstractFilter;
use AvitoAds\Model\Group;
use AvitoAds\Model\Pagination\ListRequest;
use AvitoAds\Model\Pagination\PaginatedResult;
use AvitoAds\Model\Pagination\ResultIterator;

/**
 * Методы работы с группами объявлений.
 *
 * Изменение бюджета и ставки доступно только для групп с ручным управлением ставкой.
 */
final class GroupResource extends AbstractResource
{
    /**
     * Список групп (постранично).
     *
     * POST /v1/account/{accountID}/groups
     *
     * @param array<string, mixed>|AbstractFilter|null $filter
     * @return PaginatedResult<Group>
     */
    public function list($filter = null, int $limit = ListRequest::DEFAULT_LIMIT, int $page = 1): PaginatedResult
    {
        $request = $this->listRequest($filter, $limit, $page);
        $response = $this->http->request('POST', $this->accountPath('groups'), $request->toArray());

        return $this->paginate($response, 'groups', $request, static function (array $row): Group {
            return Group::fromArray($row);
        });
    }

    /**
     * Перебор всех групп по всем страницам.
     *
     * @param array<string, mixed>|AbstractFilter|null $filter
     * @return \Generator<int, Group>
     */
    public function iterate($filter = null, int $limit = ListRequest::DEFAULT_LIMIT): \Generator
    {
        $request = $this->listRequest($filter, $limit, 1);

        return ResultIterator::iterate(function (ListRequest $req): PaginatedResult {
            $response = $this->http->request('POST', $this->accountPath('groups'), $req->toArray());

            return $this->paginate($response, 'groups', $req, static function (array $row): Group {
                return Group::fromArray($row);
            });
        }, $request);
    }

    /**
     * Изменить бюджет группы (только ручное управление ставкой).
     *
     * POST /v1/account/{accountID}/group/{groupID}/change-budget
     *
     * @param int $budget новый бюджет в рублях, не менее 1
     */
    public function changeBudget(int $groupId, int $budget): void
    {
        if ($budget < 1) {
            throw new ValidationException(sprintf('Бюджет должен быть не менее 1, передано %d.', $budget));
        }
        $this->http->request('POST', $this->accountPath('group/' . $groupId . '/change-budget'), [
            'budget' => $budget,
        ]);
    }

    /**
     * Изменить ставку группы (только ручное управление ставкой).
     *
     * POST /v1/account/{accountID}/group/{groupID}/change-price
     *
     * @param int $price новая ставка в рублях, не менее 1
     */
    public function changePrice(int $groupId, int $price): void
    {
        if ($price < 1) {
            throw new ValidationException(sprintf('Ставка должна быть не менее 1, передано %d.', $price));
        }
        $this->http->request('POST', $this->accountPath('group/' . $groupId . '/change-price'), [
            'price' => $price,
        ]);
    }
}
