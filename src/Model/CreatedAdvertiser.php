<?php

declare(strict_types=1);

namespace AvitoAds\Model;

/**
 * Результат создания рекламодателя — содержит присвоенный ID.
 */
final class CreatedAdvertiser extends AbstractModel
{
    public function getId(): int
    {
        return (int) $this->getInt('id');
    }
}
