<?php

declare(strict_types=1);

namespace AvitoAds\Model;

/**
 * Результат создания тестового аккаунта в песочнице.
 *
 * Если аккаунт создан, но часть тестовых данных (кампании, группы, креативы) не
 * подготовилась, API отвечает `207 Multi-Status`: это успех, а причины — в getWarnings().
 */
final class CreatedAccount extends AbstractModel
{
    public function getAccountId(): int
    {
        return (int) $this->getInt('accountID');
    }

    /**
     * Предупреждения о частично невыполненных sandbox-операциях; [] — аккаунт подготовлен полностью.
     *
     * @return string[]
     */
    public function getWarnings(): array
    {
        return $this->getStringList('warnings');
    }
}
