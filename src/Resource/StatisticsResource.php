<?php

declare(strict_types=1);

namespace AvitoAds\Resource;

use AvitoAds\Exception\ValidationException;
use AvitoAds\Model\CampaignStatisticResult;
use AvitoAds\Model\EntityStatistic;

/**
 * Методы получения статистики по кампаниям, группам и креативам.
 *
 * Максимальная длина периода — 100 дней; даты в формате YYYY-MM-DD.
 */
final class StatisticsResource extends AbstractResource
{
    private const MAX_PERIOD_DAYS = 100;

    /**
     * Статистика по кампании (плюс агрегаты по её группам и креативам).
     *
     * POST /v1/account/{accountID}/campaigns/{campaignID}/stats
     */
    public function campaign(int $campaignId, string $dateFrom, string $dateTo): CampaignStatisticResult
    {
        $this->assertPeriod($dateFrom, $dateTo);
        $response = $this->http->request(
            'POST',
            $this->accountPath('campaigns/' . $campaignId . '/stats'),
            ['dateFrom' => $dateFrom, 'dateTo' => $dateTo]
        );

        return CampaignStatisticResult::fromArray($response->getData());
    }

    /**
     * Статистика по группам кампании.
     *
     * POST /v1/account/{accountID}/campaigns/{campaignID}/groups/stats
     *
     * @param int[] $groupIds
     * @return EntityStatistic[]
     */
    public function groups(int $campaignId, string $dateFrom, string $dateTo, array $groupIds): array
    {
        $this->assertPeriod($dateFrom, $dateTo);
        $response = $this->http->request(
            'POST',
            $this->accountPath('campaigns/' . $campaignId . '/groups/stats'),
            ['dateFrom' => $dateFrom, 'dateTo' => $dateTo, 'groupIDs' => array_values($groupIds)]
        );

        return $this->mapEntities($response->getData()['groups'] ?? []);
    }

    /**
     * Статистика по креативам кампании.
     *
     * POST /v1/account/{accountID}/campaigns/{campaignID}/creatives/stats
     *
     * @param int[] $creativeIds
     * @return EntityStatistic[]
     */
    public function creatives(int $campaignId, string $dateFrom, string $dateTo, array $creativeIds): array
    {
        $this->assertPeriod($dateFrom, $dateTo);
        $response = $this->http->request(
            'POST',
            $this->accountPath('campaigns/' . $campaignId . '/creatives/stats'),
            ['dateFrom' => $dateFrom, 'dateTo' => $dateTo, 'creativeIDs' => array_values($creativeIds)]
        );

        return $this->mapEntities($response->getData()['creatives'] ?? []);
    }

    /**
     * @param mixed $rows
     * @return EntityStatistic[]
     */
    private function mapEntities($rows): array
    {
        if (!is_array($rows)) {
            return [];
        }

        return array_map(
            static function ($row): EntityStatistic {
                return EntityStatistic::fromArray(is_array($row) ? $row : []);
            },
            array_values($rows)
        );
    }

    private function assertPeriod(string $dateFrom, string $dateTo): void
    {
        $from = $this->parseDate($dateFrom);
        $to = $this->parseDate($dateTo);

        if ($from > $to) {
            throw new ValidationException('dateFrom не может быть позже dateTo.');
        }

        $days = (int) $from->diff($to)->format('%a') + 1;
        if ($days > self::MAX_PERIOD_DAYS) {
            throw new ValidationException(sprintf(
                'Период статистики не может превышать %d дней, запрошено %d.',
                self::MAX_PERIOD_DAYS,
                $days
            ));
        }
    }

    /**
     * Разбирает дату в формате YYYY-MM-DD, отвергая невозможные даты.
     *
     * `createFromFormat` нормализует значения вроде `2025-02-31` в `2025-03-03`,
     * поэтому одной проверки `=== false` недостаточно — нужен round-trip через
     * `format()` и проверка предупреждений `getLastErrors()`.
     */
    private function parseDate(string $value): \DateTimeImmutable
    {
        $date = \DateTimeImmutable::createFromFormat('!Y-m-d', $value);
        $errors = \DateTimeImmutable::getLastErrors();
        $hasErrors = is_array($errors) && ($errors['warning_count'] > 0 || $errors['error_count'] > 0);

        if ($date === false || $hasErrors || $date->format('Y-m-d') !== $value) {
            throw new ValidationException('Даты периода должны быть в формате YYYY-MM-DD.');
        }

        return $date;
    }
}
