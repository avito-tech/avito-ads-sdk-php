<?php

declare(strict_types=1);

namespace AvitoAds\Resource;

use AvitoAds\Model\Advertiser;
use AvitoAds\Model\CreatedAdvertiser;
use AvitoAds\Model\Filter\AbstractFilter;
use AvitoAds\Model\Pagination\ListRequest;
use AvitoAds\Model\Pagination\PaginatedResult;
use AvitoAds\Model\Pagination\ResultIterator;

/**
 * Методы работы с рекламодателями.
 */
final class AdvertiserResource extends AbstractResource
{
    /**
     * Создаёт рекламодателя.
     *
     * POST /v1/account/{accountID}/create-advertiser
     *
     * @param string      $legalType см. \AvitoAds\Enum\LegalType
     * @param string      $legalRole см. \AvitoAds\Enum\LegalRole
     * @param string|null $kpp       КПП (для ЮЛ); для ИП не требуется
     */
    public function create(
        string $inn,
        string $shortName,
        string $longName,
        string $ogrn,
        string $legalAddress,
        string $actualAddress,
        string $legalRole,
        string $legalType,
        ?string $kpp = null
    ): CreatedAdvertiser {
        $body = [
            'inn' => $inn,
            'shortName' => $shortName,
            'longName' => $longName,
            'ogrn' => $ogrn,
            'legalAddress' => $legalAddress,
            'actualAddress' => $actualAddress,
            'legalRole' => $legalRole,
            'legalType' => $legalType,
        ];
        if ($kpp !== null) {
            $body['kpp'] = $kpp;
        }

        $response = $this->http->request('POST', $this->accountPath('create-advertiser'), $body);

        return CreatedAdvertiser::fromArray($response->getData());
    }

    /**
     * Список рекламодателей (постранично).
     *
     * POST /v1/account/{accountID}/advertisers
     *
     * @param array<string, mixed>|AbstractFilter|null $filter
     * @return PaginatedResult<Advertiser>
     */
    public function list($filter = null, int $limit = ListRequest::DEFAULT_LIMIT, int $page = 1): PaginatedResult
    {
        $request = $this->listRequest($filter, $limit, $page);
        $response = $this->http->request('POST', $this->accountPath('advertisers'), $request->toArray());

        return $this->paginate($response, 'advertisers', $request, static function (array $row): Advertiser {
            return Advertiser::fromArray($row);
        });
    }

    /**
     * Перебор всех рекламодателей по всем страницам.
     *
     * @param array<string, mixed>|AbstractFilter|null $filter
     * @return \Generator<int, Advertiser>
     */
    public function iterate($filter = null, int $limit = ListRequest::DEFAULT_LIMIT): \Generator
    {
        $request = $this->listRequest($filter, $limit, 1);

        return ResultIterator::iterate(function (ListRequest $req): PaginatedResult {
            $response = $this->http->request('POST', $this->accountPath('advertisers'), $req->toArray());

            return $this->paginate($response, 'advertisers', $req, static function (array $row): Advertiser {
                return Advertiser::fromArray($row);
            });
        }, $request);
    }
}
