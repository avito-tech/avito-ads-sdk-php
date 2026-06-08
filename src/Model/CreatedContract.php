<?php

declare(strict_types=1);

namespace AvitoAds\Model;

/**
 * Результат создания договора — содержит присвоенный ID.
 */
final class CreatedContract extends AbstractModel
{
    public function getId(): int
    {
        return (int) $this->getInt('id');
    }
}
