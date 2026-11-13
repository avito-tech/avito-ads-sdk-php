<?php

declare(strict_types=1);

namespace AvitoAds\Model;

/**
 * Базовый класс моделей-ответов.
 *
 * Хранит исходные данные ответа и предоставляет типобезопасные хелперы доступа,
 * чтобы конкретные модели не дублировали приведение типов. Сырые данные всегда
 * доступны через get()/toArray() — полезно для полей, не вынесенных в геттеры.
 *
 * @phpstan-consistent-constructor
 */
abstract class AbstractModel
{
    /** @var array<string, mixed> */
    protected $data;

    /**
     * @param array<string, mixed> $data
     */
    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    /**
     * @param array<string, mixed> $data
     * @return static
     */
    public static function fromArray(array $data): self
    {
        return new static($data);
    }

    /**
     * Возвращает исходные данные ответа.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return $this->data;
    }

    /**
     * Произвольное поле сырых данных (для полей вне типизированных геттеров).
     *
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        return $this->data[$key] ?? $default;
    }

    protected function getInt(string $key): ?int
    {
        return isset($this->data[$key]) && $this->data[$key] !== null ? (int) $this->data[$key] : null;
    }

    protected function getString(string $key): ?string
    {
        return isset($this->data[$key]) && $this->data[$key] !== null ? (string) $this->data[$key] : null;
    }

    protected function getBool(string $key): ?bool
    {
        return isset($this->data[$key]) && $this->data[$key] !== null ? (bool) $this->data[$key] : null;
    }

    protected function getFloat(string $key): ?float
    {
        return isset($this->data[$key]) && $this->data[$key] !== null ? (float) $this->data[$key] : null;
    }

    /**
     * @return array<int|string, mixed>
     */
    protected function getArray(string $key): array
    {
        $value = $this->data[$key] ?? [];

        return is_array($value) ? $value : [];
    }

    /**
     * Список строк; нестроковые скаляры приводятся к строке, прочие элементы пропускаются.
     *
     * @return string[]
     */
    protected function getStringList(string $key): array
    {
        $result = [];
        foreach ($this->getArray($key) as $item) {
            if (is_scalar($item)) {
                $result[] = (string) $item;
            }
        }

        return $result;
    }

    /**
     * Список целых чисел; нечисловые элементы пропускаются.
     *
     * @return int[]
     */
    protected function getIntList(string $key): array
    {
        $result = [];
        foreach ($this->getArray($key) as $item) {
            if (is_numeric($item)) {
                $result[] = (int) $item;
            }
        }

        return $result;
    }

    /**
     * Список вложенных моделей из массива объектов.
     *
     * @template T of AbstractModel
     * @param class-string<T> $class
     * @return T[]
     */
    protected function getModelList(string $key, string $class): array
    {
        $result = [];
        foreach ($this->getArray($key) as $item) {
            $result[] = $class::fromArray(is_array($item) ? $item : []);
        }

        return $result;
    }
}
