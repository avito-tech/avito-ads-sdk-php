<?php

declare(strict_types=1);

namespace AvitoAds\Model\Filter;

/**
 * Базовый класс фильтров для list-методов.
 *
 * Фильтры опциональны: в любой list-метод можно передать и обычный массив.
 * Здесь собирается только то, что было явно задано, чтобы не отправлять
 * пустые поля.
 *
 * @phpstan-consistent-constructor
 */
abstract class AbstractFilter
{
    /** @var array<string, mixed> */
    protected $values = [];

    /**
     * @param array<string, mixed> $values
     */
    public function __construct(array $values = [])
    {
        $this->values = $values;
    }

    /**
     * @param array<string, mixed> $values
     * @return static
     */
    public static function create(array $values = []): self
    {
        return new static($values);
    }

    /**
     * @param mixed $value
     * @return static
     */
    protected function set(string $key, $value): self
    {
        $this->values[$key] = $value;

        return $this;
    }

    /**
     * Итоговый массив фильтра.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return $this->values;
    }
}
