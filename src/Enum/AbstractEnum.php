<?php

declare(strict_types=1);

namespace AvitoAds\Enum;

/**
 * Базовый класс для перечислений (enum), реализованных через константы.
 *
 * Нативные enum появились только в PHP 8.1, а библиотека поддерживает PHP 7.4,
 * поэтому перечисления оформлены как классы с константами и хелперами.
 */
abstract class AbstractEnum
{
    /**
     * Все допустимые значения перечисления.
     *
     * @return array<int, string>
     */
    public static function all(): array
    {
        /** @var array<string, string> $constants */
        $constants = (new \ReflectionClass(static::class))->getConstants();

        return array_values($constants);
    }

    /**
     * Проверяет, является ли значение допустимым для данного перечисления.
     *
     * @param mixed $value
     */
    public static function isValid($value): bool
    {
        return in_array($value, static::all(), true);
    }

    /**
     * Бросает ValidationException, если значение недопустимо.
     *
     * @param mixed $value
     * @throws \AvitoAds\Exception\ValidationException
     */
    public static function assertValid($value): void
    {
        if (!static::isValid($value)) {
            throw new \AvitoAds\Exception\ValidationException(sprintf(
                'Недопустимое значение "%s" для %s. Разрешены: %s.',
                is_scalar($value) ? (string) $value : gettype($value),
                static::class,
                implode(', ', static::all())
            ));
        }
    }
}
