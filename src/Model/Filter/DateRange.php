<?php

declare(strict_types=1);

namespace AvitoAds\Model\Filter;

/**
 * Диапазон дат {from, to} в формате YYYY-MM-DD.
 */
final class DateRange
{
    /** @var string */
    private $from;

    /** @var string */
    private $to;

    public function __construct(string $from, string $to)
    {
        $this->from = $from;
        $this->to = $to;
    }

    /**
     * @return array{from: string, to: string}
     */
    public function toArray(): array
    {
        return ['from' => $this->from, 'to' => $this->to];
    }
}
